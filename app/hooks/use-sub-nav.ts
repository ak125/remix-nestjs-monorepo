import { useMatches } from "@remix-run/react";

export function useSubNav() {
  const matches = useMatches();
  const { department = "" } = matches[1]?.params || {};

  return {
    currentDepartment: department
  };
}
