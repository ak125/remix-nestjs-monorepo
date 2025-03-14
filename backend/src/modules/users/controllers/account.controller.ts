import { Controller, Get } from "@nestjs/common";

@Controller("account-menu")
export class AccountController {
  @Get()
  getMenu() {
    return [
      { title: "Tableau de bord", link: "/dashboard" },
      { title: "Mes Commandes", link: "/orders" },
      { title: "Changer mon mot de passe", link: "/change-password" },
      { title: "Déconnexion", link: "/logout" },
    ];
  }
}
