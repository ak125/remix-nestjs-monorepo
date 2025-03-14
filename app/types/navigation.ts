export interface NavItem {
  href: string;
  label: string;
  icon?: React.ComponentType<{ className?: string }>;
}

export interface NavConfig {
  mainNav: NavItem[];
  accountNav: NavItem[];
}
