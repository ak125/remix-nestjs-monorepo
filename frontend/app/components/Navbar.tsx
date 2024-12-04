import { Link } from "@remix-run/react";
import { Bell, ReceiptEuro, UserRound } from 'lucide-react';

export const Navbar = ({ logo }: { logo: string }) => {
  return (
    <nav className="px-3 py-2 bg-blue-600 text-white flex justify-between items-center" aria-label="Navigation principale">
      <img src={logo}
        alt="Logo Automecanik"
        className="w-auto h-12"
      />
      <div className='flex gap-4'>
        <Link to='/' className="hover:text-blue-200 transition-colors"><ReceiptEuro className="flex-shrink-0" /></Link>
        <Link to='/' className="hover:text-blue-200 transition-colors"><Bell className="flex-shrink-0" /></Link>
        <Link to='/' className="hover:text-blue-200 transition-colors"><UserRound className="flex-shrink-0" /></Link>
      </div>
    </nav>
  );
};
