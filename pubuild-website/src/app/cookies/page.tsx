import Layout from '@/components/layout/layout'

export default function CookiePolicyPage() {
  return (
    <Layout>
      <div className="bg-white py-16">
        <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
          <h1 className="text-4xl font-bold text-gray-900 mb-8">
            Cookie Policy
          </h1>
          
          <div className="prose prose-lg max-w-none">
            <p className="text-gray-600 mb-6">
              <strong>Last updated:</strong> {new Date().toLocaleDateString()}
            </p>

            <section className="mb-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-4">
                What Are Cookies
              </h2>
              <p className="text-gray-700 mb-4">
                Cookies are small text files that are placed on your computer or mobile device when 
                you visit a website. They are widely used to make websites work more efficiently 
                and to provide information to website owners.
              </p>
            </section>

            <section className="mb-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-4">
                How We Use Cookies
              </h2>
              <p className="text-gray-700 mb-4">
                We use cookies to:
              </p>
              <ul className="list-disc pl-6 text-gray-700">
                <li>Remember your preferences and settings</li>
                <li>Improve website performance and functionality</li>
                <li>Analyze website traffic and usage patterns</li>
                <li>Provide personalized content and experiences</li>
                <li>Ensure website security</li>
              </ul>
            </section>

            <section className="mb-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-4">
                Types of Cookies We Use
              </h2>
              
              <div className="mb-6">
                <h3 className="text-lg font-semibold text-gray-900 mb-2">Essential Cookies</h3>
                <p className="text-gray-700 mb-2">
                  These cookies are necessary for the website to function properly. They cannot be disabled.
                </p>
                <ul className="list-disc pl-6 text-gray-700">
                  <li>Session management</li>
                  <li>Security features</li>
                  <li>Load balancing</li>
                </ul>
              </div>

              <div className="mb-6">
                <h3 className="text-lg font-semibold text-gray-900 mb-2">Analytics Cookies</h3>
                <p className="text-gray-700 mb-2">
                  These cookies help us understand how visitors interact with our website.
                </p>
                <ul className="list-disc pl-6 text-gray-700">
                  <li>Google Analytics (anonymized IP addresses)</li>
                  <li>Page views and user behavior</li>
                  <li>Traffic sources and demographics</li>
                </ul>
              </div>

              <div className="mb-6">
                <h3 className="text-lg font-semibold text-gray-900 mb-2">Functional Cookies</h3>
                <p className="text-gray-700 mb-2">
                  These cookies enable enhanced functionality and personalization.
                </p>
                <ul className="list-disc pl-6 text-gray-700">
                  <li>Language preferences</li>
                  <li>Form data retention</li>
                  <li>User interface preferences</li>
                </ul>
              </div>

              <div className="mb-6">
                <h3 className="text-lg font-semibold text-gray-900 mb-2">Marketing Cookies</h3>
                <p className="text-gray-700 mb-2">
                  These cookies are used to deliver relevant advertisements and track campaign performance.
                </p>
                <ul className="list-disc pl-6 text-gray-700">
                  <li>Advertising targeting</li>
                  <li>Campaign measurement</li>
                  <li>Social media integration</li>
                </ul>
              </div>
            </section>

            <section className="mb-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-4">
                Cookie Duration
              </h2>
              <p className="text-gray-700 mb-4">
                Cookies may be either &quot;session&quot; cookies or &quot;persistent&quot; cookies:
              </p>
              <ul className="list-disc pl-6 text-gray-700">
                <li><strong>Session cookies:</strong> Deleted when you close your browser</li>
                <li><strong>Persistent cookies:</strong> Remain on your device for a set period or until deleted</li>
              </ul>
            </section>

            <section className="mb-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-4">
                Managing Cookies
              </h2>
              <p className="text-gray-700 mb-4">
                You can control and manage cookies in various ways:
              </p>
              
              <div className="mb-4">
                <h3 className="text-lg font-semibold text-gray-900 mb-2">Browser Settings</h3>
                <p className="text-gray-700 mb-2">
                  Most browsers allow you to:
                </p>
                <ul className="list-disc pl-6 text-gray-700">
                  <li>View and delete cookies</li>
                  <li>Block cookies from specific sites</li>
                  <li>Block third-party cookies</li>
                  <li>Block all cookies</li>
                </ul>
              </div>

              <div className="mb-4">
                <h3 className="text-lg font-semibold text-gray-900 mb-2">Cookie Consent</h3>
                <p className="text-gray-700 mb-2">
                  When you first visit our website, you can choose which types of cookies to accept. 
                  You can change your preferences at any time.
                </p>
              </div>
            </section>

            <section className="mb-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-4">
                Third-Party Cookies
              </h2>
              <p className="text-gray-700 mb-4">
                Our website may contain third-party cookies from:
              </p>
              <ul className="list-disc pl-6 text-gray-700">
                <li>Google Analytics for website analytics</li>
                <li>Social media platforms for sharing features</li>
                <li>Advertising networks for targeted advertising</li>
                <li>Content delivery networks for performance</li>
              </ul>
              <p className="text-gray-700 mb-4">
                We do not control these third-party cookies. Please refer to their respective 
                privacy policies for more information.
              </p>
            </section>

            <section className="mb-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-4">
                Your Rights
              </h2>
              <p className="text-gray-700 mb-4">
                Under applicable privacy laws, you have the right to:
              </p>
              <ul className="list-disc pl-6 text-gray-700">
                <li>Know what cookies we use and why</li>
                <li>Consent to or refuse non-essential cookies</li>
                <li>Withdraw your consent at any time</li>
                <li>Request information about cookies we use</li>
                <li>Request deletion of cookie data</li>
              </ul>
            </section>

            <section className="mb-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-4">
                Updates to This Policy
              </h2>
              <p className="text-gray-700 mb-4">
                We may update this cookie policy from time to time to reflect changes in our 
                practices or for other operational, legal, or regulatory reasons. We will 
                notify you of any material changes by posting the updated policy on our website.
              </p>
            </section>

            <section className="mb-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-4">
                Contact Us
              </h2>
              <p className="text-gray-700 mb-4">
                If you have any questions about our use of cookies, please contact us:
              </p>
              <div className="bg-gray-50 rounded-lg p-6">
                <p className="text-gray-700 mb-2">
                  <strong>Email:</strong> privacy@pubuild.com
                </p>
                <p className="text-gray-700 mb-2">
                  <strong>Phone:</strong> 800-474-1388
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