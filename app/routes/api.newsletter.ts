import { json } from "@remix-run/node";
import { prisma } from "~/lib/db.server";

export async function action({ request }) {
  if (request.method !== "POST") {
    return json({ success: false, message: "Method not allowed" }, { status: 405 });
  }

  try {
    const formData = await request.formData();
    const email = formData.get("email")?.toString();

    if (!email) {
      return json(
        { success: false, message: "Email is required" },
        { status: 400 }
      );
    }

    // Validate email format
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
      return json(
        { success: false, message: "Invalid email format" },
        { status: 400 }
      );
    }

    // Check if email is already subscribed
    const existingSubscriber = await prisma.newsletter.findUnique({
      where: { email }
    });

    if (existingSubscriber) {
      return json(
        { success: false, message: "Email already subscribed" },
        { status: 409 }
      );
    }

    // Subscribe new email
    await prisma.newsletter.create({
      data: {
        email,
        status: "ACTIVE",
      }
    });

    return json({ success: true, message: "Successfully subscribed" });
  } catch (error) {
    console.error("Newsletter subscription error:", error);
    return json(
      { success: false, message: "Failed to subscribe" },
      { status: 500 }
    );
  }
}
