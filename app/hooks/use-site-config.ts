import { useState, useEffect } from "react";
import { useNavigation } from "@remix-run/react";
import type { SiteConfig } from "~/services/config.server";

const defaultConfig: SiteConfig = {
  domainName: "Automecanik",
  domain: "/core",
  domainParent: "",
  siteAddress: "",
  siteMail: "contact@example.com",
  sitePhone: "",
  sitePhoneToCall: "",
  groupName: "",
  groupDomain: "",
  ownerName: "",
  ownerDomain: "",
  accessLinks: {
    permitted: "/welcome",
    refused: "/denied",
    expired: "/expired",
    suspended: "/suspended",
    welcome: "/welcome",
  },
  currency: "€",
  vat: 20,
  vatFormatted: "20.00",
  vatCoeff: 1.2,
};

export function useSiteConfig(language: number = 1) {
  const [config, setConfig] = useState<SiteConfig>(defaultConfig);
  const [isLoading, setIsLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const navigation = useNavigation();

  useEffect(() => {
    fetch(`/api/config?lang=${language}`)
      .then((res) => {
        if (!res.ok) throw new Error("Failed to fetch config");
        return res.json();
      })
      .then((data) => {
        setConfig(data);
        setIsLoading(false);
      })
      .catch((err) => {
        console.error("Error fetching site config:", err);
        setError(err.message);
        setIsLoading(false);
      });
  }, [language]);

  return {
    config,
    isLoading: isLoading || navigation.state === "loading",
    error
  };
}
