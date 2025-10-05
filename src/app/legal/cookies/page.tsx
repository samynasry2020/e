import Link from "next/link";
import { ArrowLeft } from "lucide-react";

export const metadata = {
  title: "Cookie Policy - Pubuild",
  description: "Cookie Policy for Pubuild - How we use cookies and tracking technologies on our website.",
};

export default function CookiePolicyPage() {
  return (
    <div className="min-h-screen bg-gray-50">
      <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        {/* Back to previous page */}
        <div className="mb-8">
          <Link
            href="/"
            className="inline-flex items-center text-primary hover:underline"
          >
            <ArrowLeft className="mr-2 h-4 w-4" />
            Back to Home
          </Link>
        </div>

        <div className="bg-white rounded-lg shadow-sm p-8">
          <h1 className="text-3xl font-bold text-gray-900 mb-8">
            Cookie Policy
          </h1>

          <div className="prose prose-lg max-w-none">
            <p className="text-gray-600 mb-8">
              <strong>Last Updated:</strong> {new Date().toLocaleDateString()}
            </p>

            <section className="mb-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-4">
                1. What Are Cookies
              </h2>
              <p className="text-gray-700 mb-4">
                Cookies are small text files that are used to store small pieces of information.
                They are stored on your device when the website is loaded on your browser. These
                cookies help us make the website function properly, make it more secure, provide
                better user experience, and understand how the website performs.
              </p>
            </section>

            <section className="mb-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-4">
                2. How We Use Cookies
              </h2>
              <p className="text-gray-700 mb-4">
                We use cookies for several purposes:
              </p>

              <h3 className="text-xl font-medium text-gray-900 mb-3">
                Essential Cookies
              </h3>
              <p className="text-gray-700 mb-4">
                These cookies are necessary for the website to function properly. They enable core
                functionality such as page navigation, access to secure areas, and basic security features.
              </p>

              <h3 className="text-xl font-medium text-gray-900 mb-3">
                Performance Cookies
              </h3>
              <p className="text-gray-700 mb-4">
                These cookies collect information about how visitors use our website, such as which
                pages are visited most often. This information helps us improve website performance.
              </p>

              <h3 className="text-xl font-medium text-gray-900 mb-3">
                Functional Cookies
              </h3>
              <p className="text-gray-700 mb-4">
                These cookies allow the website to remember choices you make (such as your username,
                language, or the region you are in) and provide enhanced, more personal features.
              </p>

              <h3 className="text-xl font-medium text-gray-900 mb-3">
                Targeting Cookies
              </h3>
              <p className="text-gray-700 mb-4">
                These cookies may be set through our site by our advertising partners. They may be
                used by those companies to build a profile of your interests and show you relevant
                advertisements on other sites. (We do not use targeting cookies for advertising purposes.)
              </p>
            </section>

            <section className="mb-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-4">
                3. Types of Cookies We Use
              </h2>

              <div className="overflow-x-auto">
                <table className="min-w-full divide-y divide-gray-200">
                  <thead className="bg-gray-50">
                    <tr>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Cookie Name
                      </th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Purpose
                      </th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Duration
                      </th>
                      <th className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Type
                      </th>
                    </tr>
                  </thead>
                  <tbody className="bg-white divide-y divide-gray-200">
                    <tr>
                      <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        __Host-next-auth.csrf-token
                      </td>
                      <td className="px-6 py-4 text-sm text-gray-500">
                        Security token for form submissions
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        Session
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        Essential
                      </td>
                    </tr>
                    <tr>
                      <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        cookie-consent
                      </td>
                      <td className="px-6 py-4 text-sm text-gray-500">
                        Remembers user's cookie preferences
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        1 year
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        Functional
                      </td>
                    </tr>
                    <tr>
                      <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        _ga, _gid, _gat
                      </td>
                      <td className="px-6 py-4 text-sm text-gray-500">
                        Google Analytics for website usage tracking (if enabled)
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        Up to 2 years
                      </td>
                      <td className="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        Performance
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </section>

            <section className="mb-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-4">
                4. Managing Cookies
              </h2>
              <p className="text-gray-700 mb-4">
                You have several options for managing cookies:
              </p>

              <h3 className="text-xl font-medium text-gray-900 mb-3">
                Browser Settings
              </h3>
              <p className="text-gray-700 mb-4">
                Most web browsers allow you to control cookies through their settings preferences.
                You can typically find these settings in the "Options" or "Preferences" menu of your browser.
              </p>

              <h3 className="text-xl font-medium text-gray-900 mb-3">
                Cookie Consent Banner
              </h3>
              <p className="text-gray-700 mb-4">
                When you first visit our website, you'll see a cookie consent banner that allows you
                to accept or reject non-essential cookies.
              </p>

              <h3 className="text-xl font-medium text-gray-900 mb-3">
                Third-Party Tools
              </h3>
              <p className="text-gray-700 mb-4">
                You can use browser extensions or online tools to block or manage cookies across all websites.
              </p>
            </section>

            <section className="mb-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-4">
                5. Third-Party Cookies
              </h2>
              <p className="text-gray-700 mb-4">
                Some cookies may be set by third-party services that appear on our pages:
              </p>
              <ul className="list-disc pl-6 text-gray-700 mb-4">
                <li><strong>Google Analytics:</strong> For website analytics and performance monitoring</li>
                <li><strong>Content Delivery Networks:</strong> For faster content delivery</li>
                <li><strong>Security Services:</strong> For protection against malicious activity</li>
              </ul>
              <p className="text-gray-700">
                These third parties have their own privacy policies and cookie practices, which we
                encourage you to review.
              </p>
            </section>

            <section className="mb-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-4">
                6. Updates to This Policy
              </h2>
              <p className="text-gray-700 mb-4">
                We may update this Cookie Policy from time to time to reflect changes in our practices
                or for other operational, legal, or regulatory reasons. We will post the updated policy
                on this page and update the "Last Updated" date.
              </p>
            </section>

            <section className="mb-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-4">
                7. Contact Us
              </h2>
              <p className="text-gray-700 mb-4">
                If you have any questions about our use of cookies or this Cookie Policy, please contact us:
              </p>
              <div className="bg-gray-50 rounded-lg p-6">
                <p className="text-gray-700">
                  <strong>Email:</strong> privacy@pubuild.com<br />
                  <strong>Phone:</strong> 800-474-1388<br />
                  <strong>Subject:</strong> Cookie Policy Inquiry
                </p>
              </div>
            </section>

            <div className="border-t pt-8 text-center">
              <p className="text-gray-500 text-sm">
                This Cookie Policy is effective as of {new Date().toLocaleDateString()}.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}