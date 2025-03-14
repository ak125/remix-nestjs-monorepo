import { Link } from "@remix-run/react";
import { motion, AnimatePresence } from "framer-motion";
import { ChevronRight, Home } from "lucide-react";
import { cn } from "~/lib/utils";
import { useThemeStore } from "~/stores/theme.store";

export interface BreadcrumbItem {
  name: string;
  href?: string;
  position: number;
}

interface BreadcrumbProps {
  items: BreadcrumbItem[];
  className?: string;
}

const itemVariants = {
  hidden: { opacity: 0, x: -10 },
  visible: (i: number) => ({
    opacity: 1,
    x: 0,
    transition: {
      delay: i * 0.1,
      duration: 0.3
    }
  })
};

export function Breadcrumb({ items, className }: BreadcrumbProps) {
  const { theme, toggleTheme } = useThemeStore();

  return (
    <motion.nav
      initial={{ opacity: 0, y: -10 }}
      animate={{ opacity: 1, y: 0 }}
      transition={{ duration: 0.5 }}
      aria-label="Fil d'Ariane"
      className={cn(
        "flex items-center justify-between py-2 px-4 rounded-lg",
        theme === 'dark' ? 'bg-gray-800 text-gray-100' : 'bg-gray-50 text-gray-900',
        className
      )}
    >
      <AnimatePresence mode="wait">
        <ol
          itemScope
          itemType="https://schema.org/BreadcrumbList"
          className="flex items-center space-x-2 text-sm"
        >
          <motion.li
            variants={itemVariants}
            initial="hidden"
            animate="visible"
            custom={0}
            itemScope
            itemProp="itemListElement"
            itemType="https://schema.org/ListItem"
            className="flex items-center"
          >
            <Link 
              to="/"
              className={cn(
                "flex items-center transition-colors",
                theme === 'dark' 
                  ? 'text-gray-300 hover:text-white' 
                  : 'text-gray-600 hover:text-gray-900'
              )}
              itemProp="item"
            >
              <Home className="h-4 w-4" />
              <span className="sr-only" itemProp="name">Accueil</span>
            </Link>
            <meta itemProp="position" content="1" />
          </motion.li>

          {items.map((item, index) => (
            <motion.li
              key={item.name}
              variants={itemVariants}
              initial="hidden"
              animate="visible"
              custom={index + 1}
              itemScope
              itemProp="itemListElement"
              itemType="https://schema.org/ListItem"
              className="flex items-center"
            >
              <ChevronRight className={cn(
                "h-4 w-4 mx-2",
                theme === 'dark' ? 'text-gray-500' : 'text-gray-400'
              )} />
              {item.href ? (
                <Link
                  to={item.href}
                  className={cn(
                    "transition-colors",
                    theme === 'dark'
                      ? 'text-gray-300 hover:text-white'
                      : 'text-gray-600 hover:text-gray-900'
                  )}
                  itemProp="item"
                >
                  <span itemProp="name">{item.name}</span>
                </Link>
              ) : (
                <span 
                  itemProp="name" 
                  className={theme === 'dark' ? 'text-white' : 'text-gray-900'}
                >
                  {item.name}
                </span>
              )}
              <meta itemProp="position" content={item.position.toString()} />
            </motion.li>
          ))}
        </ol>
      </AnimatePresence>

      <button
        onClick={toggleTheme}
        className={cn(
          "p-2 rounded-md transition-colors",
          theme === 'dark'
            ? 'bg-gray-700 hover:bg-gray-600'
            : 'bg-gray-200 hover:bg-gray-300'
        )}
      >
        {theme === 'dark' ? '🌞' : '🌙'}
      </button>
    </motion.nav>
  );
}
