"use client";

import { useState, useEffect } from "react";
import { Button } from "@/components/ui/Button";
import Link from "next/link";

export function CookieBanner() {
  const [isVisible, setIsVisible] = useState(false);

  useEffect(() => {
    // Check if user has already made a choice
    const cookieConsent = localStorage.getItem("cookie-consent");
    if (!cookieConsent) {
      setIsVisible(true);
    }
  }, []);

  const acceptCookies = () => {
    localStorage.setItem("cookie-consent", "accepted");
    setIsVisible(false);
  };

  const rejectCookies = () => {
    localStorage.setItem("cookie-consent", "rejected");
    setIsVisible(false);
  };

  if (!isVisible) return null;

  return (
    <div className="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 p-4 z-50 shadow-lg">
      <div className="max-w-7xl mx-auto flex flex-col sm:flex-row items-start sm:items-center justify-between space-y-4 sm:space-y-0">
        <div className="flex-1 mr-4">
          <p className="text-sm text-gray-700">
            We use cookies to enhance your browsing experience and analyze our traffic.
            By continuing to use our site, you consent to our use of cookies.
          </p>
          <Link
            href="/legal/cookies"
            className="text-primary hover:underline text-sm mt-1 inline-block"
          >
            Learn more about our cookie policy
          </Link>
        </div>
        <div className="flex space-x-2">
          <Button variant="outline" onClick={rejectCookies}>
            Reject
          </Button>
          <Button onClick={acceptCookies}>
            Accept Cookies
          </Button>
        </div>
      </div>
    </div>
  );
}