import { createTransport } from "nodemailer";
import { renderToString } from "@react-email/components";
import { PaymentConfirmationEmail } from "~/emails/PaymentConfirmation";
import { OrderFailedEmail } from "~/emails/OrderFailed";

const transporter = createTransport({
  host: process.env.SMTP_HOST,
  port: Number(process.env.SMTP_PORT),
  secure: true,
  auth: {
    user: process.env.SMTP_USER,
    pass: process.env.SMTP_PASS,
  },
});

interface SendEmailParams {
  to: string;
  template: "payment-confirmation" | "payment-failed";
  data: Record<string, any>;
}

export async function sendEmail({ to, template, data }: SendEmailParams) {
  const templates = {
    "payment-confirmation": PaymentConfirmationEmail,
    "payment-failed": OrderFailedEmail,
  };

  const Template = templates[template];
  const html = await renderToString(Template(data));

  const result = await transporter.sendMail({
    from: process.env.EMAIL_FROM,
    to,
    subject: getSubject(template, data),
    html,
  });

  return result;
}

function getSubject(template: string, data: any) {
  switch (template) {
    case "payment-confirmation":
      return `Confirmation de paiement - Commande #${data.orderId}`;
    case "payment-failed":
      return `Échec du paiement - Commande #${data.orderId}`;
    default:
      return "Notification";
  }
}
