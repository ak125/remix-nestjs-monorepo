const { PrismaClient } = require('@prisma/client');
const bcrypt = require('bcryptjs');
const readline = require('readline');

const prisma = new PrismaClient();
const rl = readline.createInterface({
  input: process.stdin,
  output: process.stdout
});

async function main() {
  console.log('=== Création d\'un utilisateur administrateur ===');
  
  rl.question('Nom d\'utilisateur : ', async (username) => {
    rl.question('Email (optionnel) : ', async (email) => {
      rl.question('Mot de passe : ', async (password) => {
        try {
          // Vérifier si l'utilisateur existe déjà
          const existingUser = await prisma.appUser.findFirst({
            where: {
              OR: [
                { username },
                ...(email ? [{ email }] : [])
              ]
            }
          });
          
          if (existingUser) {
            console.error('Erreur : Un utilisateur avec ce nom ou cet email existe déjà.');
            process.exit(1);
          }
          
          // Créer un hash du mot de passe
          const hashedPassword = await bcrypt.hash(password, 10);
          
          // Créer l'utilisateur administrateur
          const user = await prisma.appUser.create({
            data: {
              username,
              email: email || null,
              password: hashedPassword,
              role: 'ADMIN',
              isActive: true,
            }
          });
          
          console.log(`Utilisateur administrateur créé avec succès ! ID: ${user.id}`);
          process.exit(0);
        } catch (error) {
          console.error('Erreur lors de la création de l\'utilisateur :', error);
          process.exit(1);
        } finally {
          await prisma.$disconnect();
        }
      });
    });
  });
}

main().catch(e => {
  console.error(e);
  process.exit(1);
});
