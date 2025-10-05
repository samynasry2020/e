import Link from "next/link";
import { ArrowLeft } from "lucide-react";

export const metadata = {
  title: "Accessibility Statement - Pubuild",
  description: "Accessibility Statement for Pubuild - Our commitment to making our website accessible to all users.",
};

export default function AccessibilityStatementPage() {
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
            Accessibility Statement
          </h1>

          <div className="prose prose-lg max-w-none">
            <p className="text-gray-600 mb-8">
              <strong>Last Updated:</strong> {new Date().toLocaleDateString()}
            </p>

            <section className="mb-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-4">
                Our Commitment to Accessibility
              </h2>
              <p className="text-gray-700 mb-4">
                Pubuild is committed to ensuring digital accessibility for people with disabilities.
                We are continually improving the user experience for everyone and applying the relevant
                accessibility standards to ensure we provide equal access to all users.
              </p>
              <p className="text-gray-700 mb-4">
                We strive to conform to the Web Content Accessibility Guidelines (WCAG) 2.1 Level AA
                standards. These guidelines explain how to make web content more accessible for people
                with disabilities, and user friendly for everyone.
              </p>
            </section>

            <section className="mb-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-4">
                Accessibility Features
              </h2>
              <p className="text-gray-700 mb-4">
                Our website includes the following accessibility features:
              </p>
              <ul className="list-disc pl-6 text-gray-700 mb-4">
                <li><strong>Keyboard Navigation:</strong> All interactive elements can be accessed using only a keyboard</li>
                <li><strong>Screen Reader Support:</strong> Proper heading structure and semantic HTML for screen readers</li>
                <li><strong>Color Contrast:</strong> Sufficient color contrast ratios for text readability</li>
                <li><strong>Alternative Text:</strong> Descriptive alt text for all meaningful images</li>
                <li><strong>Focus Indicators:</strong> Clear visual indicators for keyboard focus</li>
                <li><strong>Responsive Design:</strong> Works across different devices and screen sizes</li>
                <li><strong>Skip Links:</strong> Quick navigation to main content areas</li>
              </ul>
            </section>

            <section className="mb-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-4">
                Standards Compliance
              </h2>
              <p className="text-gray-700 mb-4">
                We aim to comply with:
              </p>
              <ul className="list-disc pl-6 text-gray-700 mb-4">
                <li>Web Content Accessibility Guidelines (WCAG) 2.1 Level AA</li>
                <li>Section 508 of the Rehabilitation Act</li>
                <li>Americans with Disabilities Act (ADA)</li>
                <li>EN 301 549 (European Accessibility Standard)</li>
              </ul>
            </section>

            <section className="mb-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-4">
                Testing and Validation
              </h2>
              <p className="text-gray-700 mb-4">
                We regularly test our website using:
              </p>
              <ul className="list-disc pl-6 text-gray-700 mb-4">
                <li>Automated accessibility testing tools</li>
                <li>Manual keyboard navigation testing</li>
                <li>Screen reader testing (NVDA, JAWS, VoiceOver)</li>
                <li>Color contrast validation tools</li>
                <li>User testing with people who use assistive technologies</li>
              </ul>
            </section>

            <section className="mb-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-4">
                Known Issues and Limitations
              </h2>
              <p className="text-gray-700 mb-4">
                While we strive for full accessibility, some areas may still need improvement. We are
                actively working to address any accessibility barriers. If you encounter any accessibility
                issues, please let us know so we can prioritize fixes.
              </p>
            </section>

            <section className="mb-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-4">
                Feedback and Contact Information
              </h2>
              <p className="text-gray-700 mb-4">
                We welcome your feedback on the accessibility of our website. If you encounter any
                accessibility barriers or have suggestions for improvement, please contact us:
              </p>
              <div className="bg-gray-50 rounded-lg p-6 mb-4">
                <p className="text-gray-700">
                  <strong>Email:</strong> accessibility@pubuild.com<br />
                  <strong>Phone:</strong> 800-474-1388<br />
                  <strong>Response Time:</strong> We will respond to accessibility inquiries within 48 hours
                </p>
              </div>
              <p className="text-gray-700">
                When contacting us about accessibility issues, please include:
              </p>
              <ul className="list-disc pl-6 text-gray-700">
                <li>The web page or content where you encountered the problem</li>
                <li>A description of the specific problem</li>
                <li>Your operating system and browser information</li>
                <li>Any assistive technology you were using</li>
              </ul>
            </section>

            <section className="mb-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-4">
                Technical Specifications
              </h2>
              <p className="text-gray-700 mb-4">
                This website's accessibility relies on the following technologies:
              </p>
              <ul className="list-disc pl-6 text-gray-700 mb-4">
                <li>HTML5 for semantic markup</li>
                <li>CSS3 for presentation and layout</li>
                <li>JavaScript for enhanced functionality (with graceful degradation)</li>
                <li>ARIA (Accessible Rich Internet Applications) attributes where appropriate</li>
              </ul>
              <p className="text-gray-700">
                These technologies are supported by the accessibility features of mainstream browsers
                and assistive technologies.
              </p>
            </section>

            <section className="mb-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-4">
                Continuous Improvement
              </h2>
              <p className="text-gray-700 mb-4">
                Accessibility is an ongoing process. We continuously monitor and improve our website to
                ensure it remains accessible to all users. Our development team includes accessibility
                considerations in all new features and content updates.
              </p>
            </section>

            <div className="border-t pt-8 text-center">
              <p className="text-gray-500 text-sm">
                This Accessibility Statement is effective as of {new Date().toLocaleDateString()}.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}