import nodemailer from "nodemailer";

// Configuration de Nodemailer
let transporter: nodemailer.Transporter;

// Initialiser le transporteur
function getTransporter() {
  if (transporter) return transporter;

  // Configuration pour l'environnement de production
  if (process.env.NODE_ENV === "production") {
    transporter = nodemailer.createTransport({
      host: process.env.SMTP_HOST,
      port: parseInt(process.env.SMTP_PORT || "587"),
      secure: process.env.SMTP_SECURE === "true",
      auth: {
        user: process.env.SMTP_USER,
        pass: process.env.SMTP_PASS
      }
    });
  } else {
    // Configuration pour le développement (utilisation de Ethereal ou Mailtrap)
    transporter = nodemailer.createTransport({
      host: "smtp.mailtrap.io",
      port: 2525,
      auth: {
        user: process.env.MAILTRAP_USER || "user",
        pass: process.env.MAILTRAP_PASS || "password"
      }
    });
  }

  return transporter;
}

/**
 * Envoie un email
 */
export async function sendEmail(
  to: string | string[],
  subject: string,
  text: string,
  html?: string,
  from = `Automecanik <no-reply@automecanik.com>`
) {
  const transport = getTransporter();
  
  const mailOptions = {
    from,
    to: Array.isArray(to) ? to.join(", ") : to,
    subject,
    text,
    html: html || text
  };

  try {
    const info = await transport.sendMail(mailOptions);
    return { success: true, messageId: info.messageId };
  } catch (error) {
    console.error("Erreur d'envoi d'email:", error);
    return { success: false, error };
  }
}
