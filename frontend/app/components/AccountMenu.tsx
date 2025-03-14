import { Link } from "@remix-run/react";

type MenuItem = {
  label: string;
  path: string;
};

type AccountMenuProps = {
  title: string;
  items: MenuItem[];
};

export function AccountMenu({ title, items }: AccountMenuProps) {
  return (
    <div className="my-6">
      <h2 className="text-lg font-semibold border-b pb-2">{title}</h2>
      <ul className="mt-4 space-y-2">
        {items.map((item, index) => (
          <li key={index}>
            <Link to={item.path} className="text-blue-600 hover:underline">
              {item.label}
            </Link>
          </li>
        ))}
      </ul>
    </div>
  );
}
