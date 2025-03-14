import { PrismaClient } from "@prisma/client";

const prisma = new PrismaClient();

async function main() {
  await prisma.menu.createMany({
    data: [
      { label: "Rechercher", icon: "Search", href: "/", type: "footer" },
      { label: "Offreurs", icon: "Users", href: "/", type: "footer" },
      { label: "Demandes", icon: "Plus", href: "/", type: "footer" },
      { label: "Favoris", icon: "Star", href: "/", type: "footer" },
      { label: "Message", icon: "Mail", href: "/", type: "footer" },
    ],
  });
}

main().catch((e) => {
  console.error(e);
  process.exit(1);
}).finally(async () => {
  await prisma.$disconnect();
});
