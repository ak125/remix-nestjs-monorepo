import { Injectable } from '@nestjs/common';
import { PrismaService } from '../prisma/prisma.service';
import * as docusign from 'docusign-esign';
import { z } from 'zod';

const signRequestSchema = z.object({
  email: z.string().email(),
  name: z.string().min(2),
  version: z.string(),
  language: z.string().length(2).default('fr')
});

@Injectable()
export class DocuSignService {
  private apiClient: docusign.ApiClient;

  constructor(private prisma: PrismaService) {
    this.apiClient = new docusign.ApiClient();
    this.apiClient.setBasePath(process.env.DOCUSIGN_BASE_URL!);
  }

  async sendForSignature(data: unknown) {
    const { email, name, version, language } = signRequestSchema.parse(data);

    const accessToken = await this.getAccessToken();
    this.apiClient.addDefaultHeader('Authorization', `Bearer ${accessToken}`);

    const envelopesApi = new docusign.EnvelopesApi(this.apiClient);
    const document = await this.prepareDocument(version, language);
    const signer = this.createSigner(email, name);
    
    const envelope = new docusign.EnvelopeDefinition();
    envelope.emailSubject = `Signature CGV - Version ${version}`;
    envelope.documents = [document];
    envelope.recipients = { signers: [signer] };
    envelope.status = 'sent';

    const response = await envelopesApi.createEnvelope(
      process.env.DOCUSIGN_ACCOUNT_ID!,
      { envelopeDefinition: envelope }
    );

    await this.prisma.signatureRequest.create({
      data: {
        email,
        name,
        version,
        language,
        status: 'sent',
        envelopeId: response.envelopeId
      }
    });

    return { envelopeId: response.envelopeId };
  }

  private async getAccessToken(): Promise<string> {
    // Implement JWT Grant authentication
    // ...existing token logic...
    return process.env.DOCUSIGN_ACCESS_TOKEN!;
  }

  private async prepareDocument(version: string, language: string) {
    const cgv = await this.prisma.cGV.findFirst({
      where: { version, language }
    });

    if (!cgv) {
      throw new Error(`CGV version ${version} not found`);
    }

    const document = new docusign.Document();
    document.documentBase64 = Buffer.from(cgv.content).toString('base64');
    document.name = `CGV Version ${version}`;
    document.fileExtension = 'pdf';
    document.documentId = '1';

    return document;
  }

  private createSigner(email: string, name: string) {
    const signer = new docusign.Signer();
    signer.email = email;
    signer.name = name;
    signer.recipientId = '1';
    
    const signHere = new docusign.SignHere();
    signHere.documentId = '1';
    signHere.pageNumber = '1';
    signHere.recipientId = '1';
    signHere.xPosition = '100';
    signHere.yPosition = '100';

    signer.tabs = { signHereTabs: [signHere] };

    return signer;
  }
}
