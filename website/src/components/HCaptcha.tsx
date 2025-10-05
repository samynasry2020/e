"use client";

import Script from "next/script";
import { siteConfig } from "@/lib/config";

export function HCaptcha() {
  if (!siteConfig.captcha.hcaptchaSiteKey) return null;
  return (
    <>
      <Script src="https://js.hcaptcha.com/1/api.js" strategy="afterInteractive" />
      <div className="h-captcha" data-sitekey={siteConfig.captcha.hcaptchaSiteKey} />
    </>
  );
}
