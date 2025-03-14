import { useLoaderData } from "@remix-run/react";
import { json, LoaderFunction } from "@remix-run/node";
import { Mail, Plus, Search, Star, Users } from "lucide-react";

// Mapping des icônes
const iconMap: { [key: string]: JSX.Element } = {
  Search: <Search />,
  Users: <Users />,
  Plus: <Plus />,
  Star: <Star />,
  Mail: <Mail />,
};

// Récupération des données depuis l'API
export const loader: LoaderFunction = async () => {
  const response = await fetch("http://localhost:3000/api/footer-menu");
  const data = await response.json();
  return json(data);
};

export const Footer = () => {
  const menuItems = useLoaderData<typeof loader>();

  return (
    <footer className="overflow-x-auto px-3 py-2 flex items-center justify-between gap-4 mt-auto bg-lightTurquoise">
      {menuItems.map((item) => (
        <FooterLinkItem key={item.id} href={item.href} icon={iconMap[item.icon]} label={item.label} />
      ))}
    </footer>
  );
};

const FooterLinkItem = ({
  icon,
  label,
  href,
}: {
  label: string;
  icon: JSX.Element;
  href: string;
}) => {
  return (
    <a href={href} className="flex flex-col items-center text-sm text-bleu">
      {icon} <span>{label}</span>
    </a>
  );
};
