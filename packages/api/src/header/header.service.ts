import { Injectable } from "@nestjs/common";
import { Request } from "express";
import { z } from "zod";

@Injectable()
export class HeaderService {
  private sessionSchema = z.object({
    myaklog: z.boolean().optional(),
    myakciv: z.string().optional(),
    myakprenom: z.string().optional(),
    myaknom: z.string().optional(),
    amcnkCart: z.object({
      id_article: z.array(z.number()).optional(),
    }).optional(),
  });

  getHeaderData(req: Request) {
    const parsed = this.sessionSchema.safeParse(req.session);
    if (!parsed.success) {
      return {
        loggedIn: false,
        cartItemCount: 0,
      };
    }

    const sessionData = parsed.data;
    
    return {
      loggedIn: sessionData.myaklog ?? false,
      user: sessionData.myaklog ? {
        civility: sessionData.myakciv,
        firstName: sessionData.myakprenom,
        lastName: sessionData.myaknom,
      } : null,
      cartItemCount: sessionData.amcnkCart?.id_article?.length ?? 0
    };
  }
}
