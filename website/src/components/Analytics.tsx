"use client";

import Script from "next/script";
import { siteConfig } from "@/lib/config";

export function Analytics() {
  const consent = typeof window !== "undefined" ? localStorage.getItem("cookie-consent") : null;
  if (!siteConfig.analytics.gaMeasurementId) return null;
  if (siteConfig.analytics.cookieBannerMode === "opt_in" && consent !== "accepted") return null;
  if (siteConfig.analytics.cookieBannerMode === "opt_out" && consent === "declined") return null;

  return (
    <>
      <Script src={`https://www.googletagmanager.com/gtag/js?id=${siteConfig.analytics.gaMeasurementId}`} strategy="afterInteractive" />
      <Script id="ga-setup" strategy="afterInteractive">
        {`window.dataLayer = window.dataLayer || [];function gtag(){dataLayer.push(arguments);}gtag('js', new Date());gtag('config', '${siteConfig.analytics.gaMeasurementId}', { anonymize_ip: true });`}
      </Script>
    </>
  );
}
