'use client';

import { useState, useEffect } from 'react';
import Link from 'next/link';

export default function CookieBanner() {
  const [showBanner, setShowBanner] = useState(false);
  const [preferences, setPreferences] = useState({
    necessary: true,
    analytics: false,
    marketing: false,
  });
  const [showSettings, setShowSettings] = useState(false);

  useEffect(() => {
    const consent = localStorage.getItem('cookie-consent');
    if (!consent) {
      setShowBanner(true);
    } else {
      const saved = JSON.parse(consent);
      setPreferences(saved);
      // Initialize analytics if consented
      if (saved.analytics) {
        initializeAnalytics();
      }
    }
  }, []);

  const initializeAnalytics = () => {
    // Initialize privacy-friendly analytics here (e.g., GA4 with IP anonymization)
    // Example: window.gtag('config', 'GA_MEASUREMENT_ID', { anonymize_ip: true });
    console.log('Analytics initialized with privacy settings');
  };

  const handleAcceptAll = () => {
    const newPreferences = {
      necessary: true,
      analytics: true,
      marketing: false, // Keep marketing false for privacy-first approach
    };
    savePreferences(newPreferences);
  };

  const handleRejectAll = () => {
    const newPreferences = {
      necessary: true,
      analytics: false,
      marketing: false,
    };
    savePreferences(newPreferences);
  };

  const handleSavePreferences = () => {
    savePreferences(preferences);
  };

  const savePreferences = (prefs: typeof preferences) => {
    localStorage.setItem('cookie-consent', JSON.stringify(prefs));
    setPreferences(prefs);
    setShowBanner(false);
    setShowSettings(false);

    if (prefs.analytics) {
      initializeAnalytics();
    }
  };

  if (!showBanner) {
    return null;
  }

  return (
    <div
      className="fixed bottom-0 left-0 right-0 z-50 bg-white shadow-2xl border-t-2 border-primary"
      role="dialog"
      aria-labelledby="cookie-banner-title"
      aria-describedby="cookie-banner-description"
    >
      <div className="container py-6">
        {!showSettings ? (
          <div className="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
            <div className="flex-1">
              <h2 id="cookie-banner-title" className="text-lg font-bold mb-2">
                🍪 Cookie Notice
              </h2>
              <p id="cookie-banner-description" className="text-sm text-gray-700">
                We use cookies to improve your experience and analyze site usage. Necessary cookies are always enabled.
                Analytics cookies help us understand how visitors interact with our website.{' '}
                <Link href="/cookies" className="text-primary hover:underline">
                  Learn more
                </Link>
              </p>
            </div>
            <div className="flex flex-wrap gap-2">
              <button
                onClick={() => setShowSettings(true)}
                className="btn btn-secondary text-sm"
                aria-label="Customize cookie preferences"
              >
                Customize
              </button>
              <button
                onClick={handleRejectAll}
                className="btn btn-secondary text-sm"
                aria-label="Reject optional cookies"
              >
                Reject All
              </button>
              <button
                onClick={handleAcceptAll}
                className="btn btn-primary text-sm"
                aria-label="Accept all cookies"
              >
                Accept All
              </button>
            </div>
          </div>
        ) : (
          <div>
            <h2 className="text-lg font-bold mb-4">Cookie Preferences</h2>
            <div className="space-y-4 mb-6">
              <div className="flex items-start justify-between p-4 bg-gray-50 rounded-lg">
                <div className="flex-1">
                  <h3 className="font-semibold mb-1">Necessary Cookies</h3>
                  <p className="text-sm text-gray-600">
                    Required for the website to function properly. Cannot be disabled.
                  </p>
                </div>
                <input
                  type="checkbox"
                  checked={preferences.necessary}
                  disabled
                  className="form-checkbox"
                  aria-label="Necessary cookies always enabled"
                />
              </div>

              <div className="flex items-start justify-between p-4 bg-gray-50 rounded-lg">
                <div className="flex-1">
                  <h3 className="font-semibold mb-1">Analytics Cookies</h3>
                  <p className="text-sm text-gray-600">
                    Help us understand visitor behavior with privacy-friendly analytics (IP anonymization enabled).
                  </p>
                </div>
                <input
                  type="checkbox"
                  checked={preferences.analytics}
                  onChange={(e) => setPreferences({ ...preferences, analytics: e.target.checked })}
                  className="form-checkbox"
                  aria-label="Toggle analytics cookies"
                />
              </div>

              <div className="flex items-start justify-between p-4 bg-gray-50 rounded-lg">
                <div className="flex-1">
                  <h3 className="font-semibold mb-1">Marketing Cookies</h3>
                  <p className="text-sm text-gray-600">
                    We do not use marketing or advertising cookies. Your privacy is important to us.
                  </p>
                </div>
                <input
                  type="checkbox"
                  checked={false}
                  disabled
                  className="form-checkbox"
                  aria-label="Marketing cookies not used"
                />
              </div>
            </div>

            <div className="flex gap-2">
              <button
                onClick={() => setShowSettings(false)}
                className="btn btn-secondary"
                aria-label="Cancel and go back"
              >
                Back
              </button>
              <button
                onClick={handleSavePreferences}
                className="btn btn-primary"
                aria-label="Save cookie preferences"
              >
                Save Preferences
              </button>
            </div>
          </div>
        )}
      </div>
    </div>
  );
}
