export type CookieBannerMode = "opt_in" | "opt_out";

export interface SiteConfig {
  siteName: string;
  domain: string;
  baseUrl: string;
  brand: {
    primary: string;
    secondary: string;
    accent: string;
  };
  contact: {
    salesEmail: string;
    procurementEmail: string;
    phone: string;
    addressLines: string[];
    officeHours: string;
    responseSla: string;
  };
  government: {
    uei: string;
    cage: string;
    naics: string[];
    pscFamilies: string[];
    certifications: string[];
  };
  analytics: {
    gaMeasurementId?: string;
    cookieBannerMode: CookieBannerMode;
  };
  captcha: {
    hcaptchaSiteKey?: string;
  };
  uploads: {
    maxFileBytes: number;
    allowedMimeTypes: string[];
  };
}

export const siteConfig: SiteConfig = {
  siteName: "PUBUILD",
  domain: "pubuild.com",
  baseUrl: "https://pubuild.com",
  brand: {
    primary: "#0E7490",
    secondary: "#1E293B",
    accent: "#10B981",
  },
  contact: {
    salesEmail: "sales@pubuild.com",
    procurementEmail: "procurement@pubuild.com",
    phone: "8004741388",
    addressLines: ["1234 Enterprise Way", "San Jose, CA 95134", "United States"],
    officeHours: "Mon–Fri 9:00–17:00 Pacific",
    responseSla: "Responses within one business day",
  },
  government: {
    uei: "UEI-PLACEHOLDER",
    cage: "CAGE-PLACEHOLDER",
    naics: ["334111", "423430", "541512"],
    pscFamilies: ["7B", "7C", "7D", "7E", "7F"],
    certifications: [],
  },
  analytics: {
    gaMeasurementId: process.env.NEXT_PUBLIC_GA_ID,
    cookieBannerMode: (process.env.NEXT_PUBLIC_COOKIE_BANNER_MODE as CookieBannerMode) || "opt_out",
  },
  captcha: {
    hcaptchaSiteKey: process.env.NEXT_PUBLIC_HCAPTCHA_SITEKEY,
  },
  uploads: {
    maxFileBytes: 10 * 1024 * 1024,
    allowedMimeTypes: [
      "application/pdf",
      "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
      "application/msword",
      "image/png",
      "image/jpeg",
      "text/plain",
    ],
  },
};