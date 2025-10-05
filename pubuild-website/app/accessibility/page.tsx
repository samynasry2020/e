import { generatePageMetadata } from '@/lib/metadata';
import { COMPANY } from '@/lib/constants';

export const metadata = generatePageMetadata({
  title: 'Accessibility Statement',
  description: 'PU Build is committed to ensuring digital accessibility for people with disabilities.',
  path: '/accessibility',
});

export default function AccessibilityPage() {
  const lastUpdated = 'January 1, 2025';

  return (
    <div className="section">
      <div className="container">
        <div className="max-w-4xl mx-auto">
          <h1 className="mb-4">Accessibility Statement</h1>
          <p className="text-gray-600 mb-8">Last Updated: {lastUpdated}</p>

          <div className="prose prose-lg max-w-none space-y-8">
            <section>
              <h2 className="text-2xl font-bold mb-4">Our Commitment</h2>
              <p className="text-gray-700">
                {COMPANY.name} is committed to ensuring digital accessibility for people with disabilities. We are continually 
                improving the user experience for everyone and applying the relevant accessibility standards to ensure we 
                provide equal access to all of our users.
              </p>
            </section>

            <section>
              <h2 className="text-2xl font-bold mb-4">Conformance Status</h2>
              <p className="text-gray-700 mb-4">
                We aim to conform to the{' '}
                <a
                  href="https://www.w3.org/WAI/WCAG21/quickref/"
                  target="_blank"
                  rel="noopener noreferrer"
                  className="text-primary hover:underline"
                >
                  Web Content Accessibility Guidelines (WCAG) 2.1
                </a>{' '}
                at Level AA. These guidelines explain how to make web content more accessible for people with disabilities 
                and user-friendly for everyone.
              </p>
              <p className="text-gray-700">
                WCAG 2.1 Level AA compliance means our website meets established accessibility standards for:
              </p>
              <ul className="list-disc pl-6 space-y-2 text-gray-700 mt-3">
                <li><strong>Perceivable:</strong> Information and user interface components must be presentable to users in ways they can perceive</li>
                <li><strong>Operable:</strong> User interface components and navigation must be operable</li>
                <li><strong>Understandable:</strong> Information and operation of the user interface must be understandable</li>
                <li><strong>Robust:</strong> Content must be robust enough to be interpreted by a wide variety of user agents, including assistive technologies</li>
              </ul>
            </section>

            <section>
              <h2 className="text-2xl font-bold mb-4">Accessibility Features</h2>
              <p className="text-gray-700 mb-3">Our website includes the following accessibility features:</p>
              
              <h3 className="text-xl font-semibold mb-3 mt-6">Navigation and Structure</h3>
              <ul className="list-disc pl-6 space-y-2 text-gray-700">
                <li>Semantic HTML5 markup for proper document structure</li>
                <li>Skip navigation link to jump to main content</li>
                <li>Consistent and predictable navigation throughout the site</li>
                <li>Descriptive page titles and headings</li>
                <li>Logical heading hierarchy (h1-h6)</li>
                <li>Breadcrumb navigation on product and solution pages</li>
              </ul>

              <h3 className="text-xl font-semibold mb-3 mt-6">Keyboard Accessibility</h3>
              <ul className="list-disc pl-6 space-y-2 text-gray-700">
                <li>All functionality available via keyboard</li>
                <li>Visible focus indicators on interactive elements</li>
                <li>Logical tab order throughout pages</li>
                <li>No keyboard traps</li>
              </ul>

              <h3 className="text-xl font-semibold mb-3 mt-6">Visual Design</h3>
              <ul className="list-disc pl-6 space-y-2 text-gray-700">
                <li>Sufficient color contrast ratios (WCAG AA minimum 4.5:1 for normal text)</li>
                <li>Information not conveyed by color alone</li>
                <li>Resizable text without loss of functionality</li>
                <li>Responsive design that works at 200% zoom</li>
                <li>Clear, readable fonts with adequate spacing</li>
              </ul>

              <h3 className="text-xl font-semibold mb-3 mt-6">Images and Media</h3>
              <ul className="list-disc pl-6 space-y-2 text-gray-700">
                <li>Alternative text (alt text) for all informative images</li>
                <li>Decorative images properly marked to be ignored by screen readers</li>
                <li>Descriptive link text (no &quot;click here&quot; links)</li>
              </ul>

              <h3 className="text-xl font-semibold mb-3 mt-6">Forms and Interactive Elements</h3>
              <ul className="list-disc pl-6 space-y-2 text-gray-700">
                <li>Properly labeled form fields with associated labels</li>
                <li>Clear error messages and validation feedback</li>
                <li>Required fields clearly indicated</li>
                <li>ARIA attributes for enhanced screen reader support</li>
                <li>Accessible form validation and error handling</li>
              </ul>

              <h3 className="text-xl font-semibold mb-3 mt-6">Assistive Technology Support</h3>
              <ul className="list-disc pl-6 space-y-2 text-gray-700">
                <li>Compatible with screen readers (JAWS, NVDA, VoiceOver)</li>
                <li>ARIA landmarks for page regions</li>
                <li>Proper role attributes for custom components</li>
                <li>Live regions for dynamic content updates</li>
              </ul>
            </section>

            <section>
              <h2 className="text-2xl font-bold mb-4">Known Limitations</h2>
              <p className="text-gray-700 mb-3">
                While we strive for full accessibility, we acknowledge that some aspects of our website may not yet meet 
                all accessibility standards. We are actively working to address these issues:
              </p>
              <ul className="list-disc pl-6 space-y-2 text-gray-700">
                <li>Some third-party embedded content may not be fully accessible</li>
                <li>Downloadable PDF documents may require accessibility improvements</li>
                <li>Some legacy content may need updating for full compliance</li>
              </ul>
            </section>

            <section>
              <h2 className="text-2xl font-bold mb-4">Assistive Technologies</h2>
              <p className="text-gray-700 mb-3">
                Our website is designed to be compatible with the following assistive technologies:
              </p>
              <ul className="list-disc pl-6 space-y-2 text-gray-700">
                <li>Screen readers (JAWS, NVDA, VoiceOver, TalkBack)</li>
                <li>Screen magnification software</li>
                <li>Speech recognition software</li>
                <li>Keyboard-only navigation</li>
                <li>Alternative input devices</li>
              </ul>
            </section>

            <section>
              <h2 className="text-2xl font-bold mb-4">Testing and Compliance</h2>
              <p className="text-gray-700">
                We regularly test our website using:
              </p>
              <ul className="list-disc pl-6 space-y-2 text-gray-700 mt-3">
                <li>Automated accessibility testing tools (axe, Lighthouse)</li>
                <li>Manual testing with keyboard navigation</li>
                <li>Screen reader testing</li>
                <li>Color contrast analysis</li>
                <li>User feedback from people with disabilities</li>
              </ul>
            </section>

            <section>
              <h2 className="text-2xl font-bold mb-4">Feedback and Contact</h2>
              <p className="text-gray-700 mb-4">
                We welcome your feedback on the accessibility of {COMPANY.name}'s website. If you encounter accessibility 
                barriers or have suggestions for improvement, please let us know:
              </p>
              <div className="bg-blue-50 border border-blue-200 rounded-lg p-6">
                <h3 className="font-bold mb-3">Report Accessibility Issues</h3>
                <p className="text-gray-700 mb-3">
                  <strong>Email:</strong>{' '}
                  <a href={`mailto:${COMPANY.email}?subject=Accessibility%20Feedback`} className="text-primary hover:underline">
                    {COMPANY.email}
                  </a>
                </p>
                <p className="text-gray-700 mb-3">
                  <strong>Phone:</strong>{' '}
                  <a href={`tel:${COMPANY.phoneRaw}`} className="text-primary hover:underline">
                    {COMPANY.phone}
                  </a>
                </p>
                <p className="text-gray-700 mb-3">
                  <strong>Subject Line:</strong> &quot;Accessibility Feedback&quot; or &quot;Accessibility Issue&quot;
                </p>
                <p className="text-gray-700 text-sm">
                  When reporting an accessibility issue, please include:
                </p>
                <ul className="list-disc pl-6 space-y-1 text-gray-700 text-sm mt-2">
                  <li>The page URL where you encountered the issue</li>
                  <li>A description of the problem</li>
                  <li>The assistive technology you were using (if applicable)</li>
                  <li>Your browser and operating system</li>
                </ul>
              </div>
              <p className="text-gray-700 mt-4">
                We aim to respond to accessibility feedback within 5 business days and to provide a resolution or 
                alternative access method within 10 business days when possible.
              </p>
            </section>

            <section>
              <h2 className="text-2xl font-bold mb-4">Alternative Access</h2>
              <p className="text-gray-700">
                If you encounter content or functionality that you cannot access due to a disability, we will work with 
                you to provide the information or service in an alternative format. Please contact us using the information 
                above, and we will assist you promptly.
              </p>
            </section>

            <section>
              <h2 className="text-2xl font-bold mb-4">Ongoing Efforts</h2>
              <p className="text-gray-700">
                Accessibility is an ongoing effort. We are committed to:
              </p>
              <ul className="list-disc pl-6 space-y-2 text-gray-700 mt-3">
                <li>Regularly auditing our website for accessibility compliance</li>
                <li>Training our team on accessibility best practices</li>
                <li>Incorporating accessibility into our design and development process</li>
                <li>Updating content and features to improve accessibility</li>
                <li>Staying informed about evolving accessibility standards</li>
              </ul>
            </section>

            <section>
              <h2 className="text-2xl font-bold mb-4">Third-Party Content</h2>
              <p className="text-gray-700">
                Some content on our website may be provided by third parties. While we work with vendors to ensure 
                accessibility, we may not have complete control over third-party content. If you encounter accessibility 
                issues with third-party content, please let us know, and we will work with our partners to address the issue.
              </p>
            </section>

            <section>
              <h2 className="text-2xl font-bold mb-4">Legal Standards</h2>
              <p className="text-gray-700">
                {COMPANY.name} strives to comply with applicable accessibility laws and regulations, including:
              </p>
              <ul className="list-disc pl-6 space-y-2 text-gray-700 mt-3">
                <li>Americans with Disabilities Act (ADA)</li>
                <li>Section 508 of the Rehabilitation Act</li>
                <li>Web Content Accessibility Guidelines (WCAG) 2.1 Level AA</li>
              </ul>
            </section>

            <section className="bg-gray-50 p-6 rounded-lg">
              <h2 className="text-2xl font-bold mb-4">Accommodation Requests</h2>
              <p className="text-gray-700">
                If you need a reasonable accommodation to access our products or services due to a disability, please 
                contact us. We will work with you to provide access in a timely manner.
              </p>
              <p className="text-gray-700 mt-3">
                For government procurement, we can provide accessibility documentation and VPATs (Voluntary Product 
                Accessibility Templates) upon request.
              </p>
            </section>
          </div>
        </div>
      </div>
    </div>
  );
}
