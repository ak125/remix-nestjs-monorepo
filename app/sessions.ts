import { createCookieSessionStorage } from "@remix-run/node";

// Définition des options de session sécurisées
const { getSession, commitSession, destroySession } = createCookieSessionStorage({
  cookie: {
    name: "__cart_session",
    // Sécurisation des cookies
    httpOnly: true,
    path: "/",
    sameSite: "lax",
    secrets: [process.env.SESSION_SECRET || "s3cr3t-k3y-f0r-d3v"],
    secure: process.env.NODE_ENV === "production",
    // Configuration de l'expiration
    maxAge: 60 * 60 * 24 * 7, // 7 jours
  },
});

export { getSession, commitSession, destroySession };
