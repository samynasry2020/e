import { generatePageMetadata } from '@/lib/metadata';
import { COMPANY } from '@/lib/constants';

export const metadata = generatePageMetadata({
  title: 'Cookie Policy',
  description: 'Learn about how PU Build uses cookies and similar tracking technologies.',
  path: '/cookies',
});

export default function CookiesPage() {
  const lastUpdated = 'January 1, 2025';

  return (
    <div className="section">
      <div className="container">
        <div className="max-w-4xl mx-auto">
          <h1 className="mb-4">Cookie Policy</h1>
          <p className="text-gray-600 mb-8">Last Updated: {lastUpdated}</p>

          <div className="prose prose-lg max-w-none space-y-8">
            <section>
              <h2 className="text-2xl font-bold mb-4">What Are Cookies?</h2>
              <p className="text-gray-700">
                Cookies are small text files that are placed on your device (computer, smartphone, or tablet) when you 
                visit a website. Cookies are widely used to make websites work more efficiently and to provide information 
                to website owners.
              </p>
              <p className="text-gray-700 mt-3">
                This Cookie Policy explains how {COMPANY.name} uses cookies and similar tracking technologies on our website 
                {COMPANY.domain}.
              </p>
            </section>

            <section>
              <h2 className="text-2xl font-bold mb-4">Types of Cookies We Use</h2>

              <div className="space-y-6">
                <div className="bg-green-50 border border-green-200 rounded-lg p-6">
                  <h3 className="text-xl font-semibold mb-3 flex items-center">
                    <span className="text-2xl mr-3">✅</span>
                    Strictly Necessary Cookies
                  </h3>
                  <p className="text-gray-700 mb-3">
                    <strong>Purpose:</strong> These cookies are essential for the website to function properly. They enable 
                    basic functions like page navigation, security, and access to secure areas.
                  </p>
                  <p className="text-gray-700 mb-3">
                    <strong>Can be disabled:</strong> No - these are required for the website to work.
                  </p>
                  <p className="text-gray-700 mb-3">
                    <strong>Examples:</strong>
                  </p>
                  <ul className="list-disc pl-6 space-y-1 text-gray-700">
                    <li>Session cookies for maintaining your browsing session</li>
                    <li>Cookie consent preferences</li>
                    <li>Security cookies to prevent fraud and protect your data</li>
                    <li>Load balancing cookies for website performance</li>
                  </ul>
                </div>

                <div className="bg-blue-50 border border-blue-200 rounded-lg p-6">
                  <h3 className="text-xl font-semibold mb-3 flex items-center">
                    <span className="text-2xl mr-3">📊</span>
                    Analytics Cookies
                  </h3>
                  <p className="text-gray-700 mb-3">
                    <strong>Purpose:</strong> These cookies help us understand how visitors interact with our website by 
                    collecting and reporting information anonymously.
                  </p>
                  <p className="text-gray-700 mb-3">
                    <strong>Can be disabled:</strong> Yes - you can opt out via our cookie banner.
                  </p>
                  <p className="text-gray-700 mb-3">
                    <strong>Privacy measures:</strong>
                  </p>
                  <ul className="list-disc pl-6 space-y-1 text-gray-700">
                    <li>IP addresses are anonymized</li>
                    <li>No personally identifiable information is collected</li>
                    <li>Data is aggregated and used for statistical purposes only</li>
                    <li>Compliance with privacy regulations (GDPR, CCPA)</li>
                  </ul>
                  <p className="text-gray-700 mt-3">
                    <strong>Examples:</strong> Google Analytics (with IP anonymization), website performance monitoring
                  </p>
                </div>

                <div className="bg-gray-50 border border-gray-300 rounded-lg p-6">
                  <h3 className="text-xl font-semibold mb-3 flex items-center">
                    <span className="text-2xl mr-3">🚫</span>
                    Marketing / Advertising Cookies
                  </h3>
                  <p className="text-gray-700 mb-3">
                    <strong>Purpose:</strong> These cookies track your browsing habits to display relevant advertisements.
                  </p>
                  <p className="text-gray-700 mb-3">
                    <strong>Our policy:</strong> <strong className="text-primary">We do not use marketing or advertising 
                    cookies.</strong> We respect your privacy and do not track you across websites for advertising purposes.
                  </p>
                </div>
              </div>
            </section>

            <section>
              <h2 className="text-2xl font-bold mb-4">Cookies We Use</h2>
              <p className="text-gray-700 mb-4">Below is a detailed list of cookies used on our website:</p>

              <div className="overflow-x-auto">
                <table className="min-w-full bg-white border border-gray-300">
                  <thead className="bg-gray-100">
                    <tr>
                      <th className="px-4 py-3 text-left font-semibold border-b">Cookie Name</th>
                      <th className="px-4 py-3 text-left font-semibold border-b">Category</th>
                      <th className="px-4 py-3 text-left font-semibold border-b">Purpose</th>
                      <th className="px-4 py-3 text-left font-semibold border-b">Duration</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr className="border-b">
                      <td className="px-4 py-3">cookie-consent</td>
                      <td className="px-4 py-3">Necessary</td>
                      <td className="px-4 py-3">Stores your cookie preferences</td>
                      <td className="px-4 py-3">1 year</td>
                    </tr>
                    <tr className="border-b">
                      <td className="px-4 py-3">_ga</td>
                      <td className="px-4 py-3">Analytics</td>
                      <td className="px-4 py-3">Google Analytics - distinguishes users (anonymized)</td>
                      <td className="px-4 py-3">2 years</td>
                    </tr>
                    <tr className="border-b">
                      <td className="px-4 py-3">_gid</td>
                      <td className="px-4 py-3">Analytics</td>
                      <td className="px-4 py-3">Google Analytics - distinguishes users (anonymized)</td>
                      <td className="px-4 py-3">24 hours</td>
                    </tr>
                    <tr className="border-b">
                      <td className="px-4 py-3">_gat</td>
                      <td className="px-4 py-3">Analytics</td>
                      <td className="px-4 py-3">Google Analytics - throttles request rate</td>
                      <td className="px-4 py-3">1 minute</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </section>

            <section>
              <h2 className="text-2xl font-bold mb-4">How to Manage Cookies</h2>
              
              <h3 className="text-xl font-semibold mb-3">Via Our Cookie Banner</h3>
              <p className="text-gray-700 mb-4">
                When you first visit our website, you'll see a cookie consent banner. You can:
              </p>
              <ul className="list-disc pl-6 space-y-2 text-gray-700">
                <li><strong>Accept All:</strong> Enables all cookies including analytics</li>
                <li><strong>Reject All:</strong> Only necessary cookies will be used</li>
                <li><strong>Customize:</strong> Choose which categories of cookies to enable</li>
              </ul>
              <p className="text-gray-700 mt-4">
                You can change your preferences at any time by clicking the &quot;Cookie Settings&quot; link in the footer 
                or clearing your browser cookies.
              </p>

              <h3 className="text-xl font-semibold mb-3 mt-6">Via Your Browser</h3>
              <p className="text-gray-700 mb-3">
                Most web browsers allow you to control cookies through their settings. You can:
              </p>
              <ul className="list-disc pl-6 space-y-2 text-gray-700 mb-4">
                <li>Block all cookies</li>
                <li>Block third-party cookies only</li>
                <li>Delete cookies when you close your browser</li>
                <li>Review and delete individual cookies</li>
              </ul>
              <p className="text-gray-700 mb-3">
                <strong>Browser-specific instructions:</strong>
              </p>
              <ul className="list-disc pl-6 space-y-2 text-gray-700">
                <li>
                  <strong>Chrome:</strong>{' '}
                  <a
                    href="https://support.google.com/chrome/answer/95647"
                    target="_blank"
                    rel="noopener noreferrer"
                    className="text-primary hover:underline"
                  >
                    Manage cookies in Chrome
                  </a>
                </li>
                <li>
                  <strong>Firefox:</strong>{' '}
                  <a
                    href="https://support.mozilla.org/en-US/kb/enhanced-tracking-protection-firefox-desktop"
                    target="_blank"
                    rel="noopener noreferrer"
                    className="text-primary hover:underline"
                  >
                    Manage cookies in Firefox
                  </a>
                </li>
                <li>
                  <strong>Safari:</strong>{' '}
                  <a
                    href="https://support.apple.com/guide/safari/manage-cookies-sfri11471/mac"
                    target="_blank"
                    rel="noopener noreferrer"
                    className="text-primary hover:underline"
                  >
                    Manage cookies in Safari
                  </a>
                </li>
                <li>
                  <strong>Edge:</strong>{' '}
                  <a
                    href="https://support.microsoft.com/en-us/microsoft-edge/delete-cookies-in-microsoft-edge-63947406-40ac-c3b8-57b9-2a946a29ae09"
                    target="_blank"
                    rel="noopener noreferrer"
                    className="text-primary hover:underline"
                  >
                    Manage cookies in Edge
                  </a>
                </li>
              </ul>
              <p className="text-sm text-gray-600 mt-4">
                <strong>Note:</strong> Blocking all cookies may affect website functionality. Some features may not work properly 
                if cookies are disabled.
              </p>
            </section>

            <section>
              <h2 className="text-2xl font-bold mb-4">Third-Party Cookies</h2>
              <p className="text-gray-700 mb-4">
                Some cookies on our website are set by third-party services that appear on our pages. We use:
              </p>
              <ul className="list-disc pl-6 space-y-2 text-gray-700">
                <li>
                  <strong>Google Analytics:</strong> To understand website traffic and usage patterns (with IP anonymization)
                </li>
              </ul>
              <p className="text-gray-700 mt-4">
                We do not control these third-party cookies. Please review the privacy policies of these services:
              </p>
              <ul className="list-disc pl-6 space-y-2 text-gray-700 mt-3">
                <li>
                  <a
                    href="https://policies.google.com/privacy"
                    target="_blank"
                    rel="noopener noreferrer"
                    className="text-primary hover:underline"
                  >
                    Google Privacy Policy
                  </a>
                </li>
              </ul>
            </section>

            <section>
              <h2 className="text-2xl font-bold mb-4">Do Not Track (DNT)</h2>
              <p className="text-gray-700">
                Some browsers offer a &quot;Do Not Track&quot; (DNT) setting. While there is no universal standard for DNT, 
                we respect your privacy choices. If you have enabled DNT, we will not use analytics cookies without your 
                explicit consent via our cookie banner.
              </p>
            </section>

            <section>
              <h2 className="text-2xl font-bold mb-4">Updates to This Policy</h2>
              <p className="text-gray-700">
                We may update this Cookie Policy from time to time to reflect changes in technology, law, or our practices. 
                We will post updates on this page with a revised &quot;Last Updated&quot; date. Please review this policy 
                periodically.
              </p>
            </section>

            <section>
              <h2 className="text-2xl font-bold mb-4">Contact Us</h2>
              <p className="text-gray-700 mb-4">
                If you have questions about our use of cookies, please contact us:
              </p>
              <div className="bg-gray-50 p-6 rounded-lg">
                <p className="text-gray-700"><strong>{COMPANY.legalName}</strong></p>
                <p className="text-gray-700">Email: <a href={`mailto:${COMPANY.email}`} className="text-primary hover:underline">{COMPANY.email}</a></p>
                <p className="text-gray-700">Phone: {COMPANY.phone}</p>
              </div>
            </section>

            <section className="bg-blue-50 border border-blue-200 rounded-lg p-6">
              <h2 className="text-2xl font-bold mb-4">Our Privacy-First Approach</h2>
              <p className="text-gray-700 mb-3">
                At {COMPANY.name}, we believe in privacy by default:
              </p>
              <ul className="list-disc pl-6 space-y-2 text-gray-700">
                <li>✓ We only use cookies that serve a clear purpose</li>
                <li>✓ We anonymize analytics data (IP addresses are masked)</li>
                <li>✓ We do not use advertising or marketing cookies</li>
                <li>✓ We do not sell or share your data with third parties</li>
                <li>✓ We give you full control over non-essential cookies</li>
                <li>✓ We are transparent about what data we collect and why</li>
              </ul>
              <p className="text-gray-700 mt-4">
                For more information about how we handle your personal data, see our{' '}
                <a href="/privacy" className="text-primary hover:underline">Privacy Policy</a>.
              </p>
            </section>
          </div>
        </div>
      </div>
    </div>
  );
}
