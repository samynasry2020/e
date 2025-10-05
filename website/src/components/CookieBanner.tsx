"use client";

import { useEffect, useState } from "react";
import { siteConfig } from "@/lib/config";

export function CookieBanner() {
  const [visible, setVisible] = useState(false);
  const [choice, setChoice] = useState<string | null>(null);

  useEffect(() => {
    const c = localStorage.getItem("cookie-consent");
    setChoice(c);
    if (!c) setVisible(true);
  }, []);

  function accept() {
    localStorage.setItem("cookie-consent", "accepted");
    setChoice("accepted");
    setVisible(false);
  }

  function decline() {
    localStorage.setItem("cookie-consent", "declined");
    setChoice("declined");
    setVisible(false);
  }

  if (!visible) return null;

  const isOptIn = siteConfig.analytics.cookieBannerMode === "opt_in";

  return (
    <div className="fixed inset-x-0 bottom-0 z-50">
      <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pb-6">
        <div className="rounded-md border border-slate-300 bg-white p-4 shadow">
          <p className="text-sm text-slate-700">
            We use cookies for necessary operations and optional analytics. Manage your preferences.
          </p>
          <div className="mt-3 flex gap-2">
            <button className="btn-primary inline-flex items-center rounded px-3 py-1.5 text-sm" onClick={accept}>
              {isOptIn ? "Allow all" : "Got it"}
            </button>
            <button className="inline-flex items-center rounded border border-slate-300 px-3 py-1.5 text-sm hover:bg-slate-50" onClick={decline}>
              {isOptIn ? "Decline" : "Manage later"}
            </button>
          </div>
        </div>
      </div>
    </div>
  );
}
