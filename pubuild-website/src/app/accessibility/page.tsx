import Layout from '@/components/layout/layout'

export default function AccessibilityPage() {
  return (
    <Layout>
      <div className="bg-white py-16">
        <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
          <h1 className="text-4xl font-bold text-gray-900 mb-8">
            Accessibility Statement
          </h1>
          
          <div className="prose prose-lg max-w-none">
            <p className="text-gray-600 mb-6">
              <strong>Last updated:</strong> {new Date().toLocaleDateString()}
            </p>

            <section className="mb-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-4">
                Our Commitment
              </h2>
              <p className="text-gray-700 mb-4">
                Pubuild is committed to ensuring digital accessibility for people with disabilities. 
                We are continually improving the user experience for everyone, and applying the 
                relevant accessibility standards to ensure we provide equal access to all users.
              </p>
            </section>

            <section className="mb-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-4">
                Conformance Status
              </h2>
              <p className="text-gray-700 mb-4">
                The Web Content Accessibility Guidelines (WCAG) defines requirements for designers 
                and developers to improve accessibility for people with disabilities. It defines 
                three levels of conformance: Level A, Level AA, and Level AAA. Our website is 
                partially conformant with WCAG 2.1 level AA. Partially conformant means that 
                some parts of the content do not fully conform to the accessibility standard.
              </p>
            </section>

            <section className="mb-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-4">
                Accessibility Features
              </h2>
              <p className="text-gray-700 mb-4">
                We have implemented the following accessibility features:
              </p>
              <ul className="list-disc pl-6 text-gray-700">
                <li>Semantic HTML structure for screen readers</li>
                <li>Alternative text for images and graphics</li>
                <li>Keyboard navigation support</li>
                <li>High contrast color schemes</li>
                <li>Focus indicators for interactive elements</li>
                <li>Skip links for main content</li>
                <li>Form labels and error messages</li>
                <li>Responsive design for various screen sizes</li>
              </ul>
            </section>

            <section className="mb-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-4">
                Known Issues
              </h2>
              <p className="text-gray-700 mb-4">
                We are aware of the following accessibility issues and are working to address them:
              </p>
              <ul className="list-disc pl-6 text-gray-700">
                <li>Some PDF documents may not be fully accessible to screen readers</li>
                <li>Some third-party content may not meet accessibility standards</li>
                <li>Some complex data tables may need additional markup</li>
              </ul>
            </section>

            <section className="mb-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-4">
                Feedback
              </h2>
              <p className="text-gray-700 mb-4">
                We welcome your feedback on the accessibility of our website. Please let us know 
                if you encounter accessibility barriers:
              </p>
              <ul className="list-disc pl-6 text-gray-700">
                <li>Phone: 800-474-1388</li>
                <li>Email: accessibility@pubuild.com</li>
                <li>Contact form: <a href="/contact" className="text-primary hover:underline">Contact Us</a></li>
              </ul>
            </section>

            <section className="mb-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-4">
                Assessment Approach
              </h2>
              <p className="text-gray-700 mb-4">
                Pubuild assessed the accessibility of this website through the following approaches:
              </p>
              <ul className="list-disc pl-6 text-gray-700">
                <li>Self-evaluation using automated accessibility testing tools</li>
                <li>Manual testing with keyboard-only navigation</li>
                <li>Testing with screen reader software</li>
                <li>User testing with people with disabilities</li>
              </ul>
            </section>

            <section className="mb-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-4">
                Alternative Formats
              </h2>
              <p className="text-gray-700 mb-4">
                If you need information from our website in an alternative format, please contact us. 
                We can provide:
              </p>
              <ul className="list-disc pl-6 text-gray-700">
                <li>Large print versions of documents</li>
                <li>Audio descriptions of visual content</li>
                <li>Alternative text formats for complex information</li>
                <li>Assistance with form completion</li>
              </ul>
            </section>

            <section className="mb-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-4">
                Third-Party Content
              </h2>
              <p className="text-gray-700 mb-4">
                Our website may contain third-party content or links to third-party websites. 
                We are not responsible for the accessibility of third-party content. If you 
                encounter accessibility issues with third-party content, please contact the 
                respective third-party provider.
              </p>
            </section>

            <section className="mb-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-4">
                Updates
              </h2>
              <p className="text-gray-700 mb-4">
                We regularly review and update our website to improve accessibility. This 
                statement will be updated as we make improvements and address known issues.
              </p>
            </section>

            <section className="mb-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-4">
                Contact Information
              </h2>
              <p className="text-gray-700 mb-4">
                For accessibility-related questions or to report accessibility issues:
              </p>
              <div className="bg-gray-50 rounded-lg p-6">
                <p className="text-gray-700 mb-2">
                  <strong>Accessibility Coordinator:</strong> accessibility@pubuild.com
                </p>
                <p className="text-gray-700 mb-2">
                  <strong>Phone:</strong> 800-474-1388
                </p>
                <p className="text-gray-700 mb-2">
                  <strong>Response Time:</strong> Within 48 hours
                </p>
                <p className="text-gray-700">
                  <strong>Address:</strong> California, USA
                </p>
              </div>
            </section>
          </div>
        </div>
      </div>
    </Layout>
  )
}