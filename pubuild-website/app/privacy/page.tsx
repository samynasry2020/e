import { generatePageMetadata } from '@/lib/metadata';
import { COMPANY } from '@/lib/constants';

export const metadata = generatePageMetadata({
  title: 'Privacy Policy',
  description: 'PU Build Privacy Policy - How we collect, use, and protect your personal information.',
  path: '/privacy',
});

export default function PrivacyPage() {
  const lastUpdated = 'January 1, 2025';

  return (
    <div className="section">
      <div className="container">
        <div className="max-w-4xl mx-auto">
          <h1 className="mb-4">Privacy Policy</h1>
          <p className="text-gray-600 mb-8">Last Updated: {lastUpdated}</p>

          <div className="prose prose-lg max-w-none space-y-8">
            <section>
              <h2 className="text-2xl font-bold mb-4">1. Introduction</h2>
              <p className="text-gray-700 mb-4">
                {COMPANY.legalName} (&quot;{COMPANY.name},&quot; &quot;we,&quot; &quot;our,&quot; or &quot;us&quot;) is committed to protecting your privacy. 
                This Privacy Policy explains how we collect, use, disclose, and safeguard your information when you visit 
                our website {COMPANY.domain} and use our services.
              </p>
              <p className="text-gray-700">
                This policy complies with applicable privacy laws including the California Consumer Privacy Act (CCPA/CPRA) 
                and other state and federal regulations.
              </p>
            </section>

            <section>
              <h2 className="text-2xl font-bold mb-4">2. Information We Collect</h2>
              
              <h3 className="text-xl font-semibold mb-3">2.1 Information You Provide</h3>
              <p className="text-gray-700 mb-3">We collect information you voluntarily provide when you:</p>
              <ul className="list-disc pl-6 space-y-2 text-gray-700 mb-4">
                <li>Fill out contact forms or request quotes</li>
                <li>Submit RFP or bid requests</li>
                <li>Subscribe to our newsletter</li>
                <li>Communicate with us via email or phone</li>
                <li>Create an account (if applicable)</li>
              </ul>
              <p className="text-gray-700 mb-4">This information may include:</p>
              <ul className="list-disc pl-6 space-y-2 text-gray-700">
                <li>Name and contact information (email, phone number, mailing address)</li>
                <li>Company or organization name</li>
                <li>Job title and department</li>
                <li>Project requirements and specifications</li>
                <li>Any other information you choose to provide</li>
              </ul>

              <h3 className="text-xl font-semibold mb-3 mt-6">2.2 Automatically Collected Information</h3>
              <p className="text-gray-700 mb-3">When you visit our website, we may automatically collect:</p>
              <ul className="list-disc pl-6 space-y-2 text-gray-700">
                <li>Device information (browser type, operating system)</li>
                <li>IP address (anonymized for analytics)</li>
                <li>Pages visited and time spent on pages</li>
                <li>Referral source (how you found our website)</li>
                <li>Cookies and similar tracking technologies (see Cookie Policy)</li>
              </ul>
            </section>

            <section>
              <h2 className="text-2xl font-bold mb-4">3. How We Use Your Information</h2>
              <p className="text-gray-700 mb-3">We use your information for the following purposes:</p>
              <ul className="list-disc pl-6 space-y-2 text-gray-700">
                <li><strong>Business Operations:</strong> To process quotes, respond to inquiries, and fulfill orders</li>
                <li><strong>Communication:</strong> To contact you regarding your requests and provide customer support</li>
                <li><strong>Marketing:</strong> To send newsletters and promotional materials (with your consent)</li>
                <li><strong>Analytics:</strong> To understand website usage and improve our services</li>
                <li><strong>Legal Compliance:</strong> To comply with legal obligations and protect our rights</li>
                <li><strong>Security:</strong> To detect and prevent fraud, abuse, and security incidents</li>
              </ul>
            </section>

            <section>
              <h2 className="text-2xl font-bold mb-4">4. Information Sharing and Disclosure</h2>
              <p className="text-gray-700 mb-4">
                <strong>We do not sell your personal information.</strong> We may share your information only in the following circumstances:
              </p>
              <ul className="list-disc pl-6 space-y-2 text-gray-700">
                <li><strong>Service Providers:</strong> Third-party vendors who assist with website hosting, email delivery, analytics, and payment processing (subject to confidentiality agreements)</li>
                <li><strong>Business Partners:</strong> Manufacturers and distributors when fulfilling your orders (only information necessary for order fulfillment)</li>
                <li><strong>Legal Requirements:</strong> When required by law, court order, or government request</li>
                <li><strong>Business Transfers:</strong> In connection with a merger, acquisition, or sale of assets</li>
                <li><strong>Consent:</strong> When you have given explicit consent to share your information</li>
              </ul>
            </section>

            <section>
              <h2 className="text-2xl font-bold mb-4">5. Data Retention</h2>
              <p className="text-gray-700">
                We retain your personal information for as long as necessary to fulfill the purposes outlined in this policy, 
                unless a longer retention period is required by law. Generally:
              </p>
              <ul className="list-disc pl-6 space-y-2 text-gray-700 mt-3">
                <li>Contact form submissions: 3 years</li>
                <li>Quote and RFP requests: 7 years (for business records)</li>
                <li>Newsletter subscriptions: Until you unsubscribe</li>
                <li>Analytics data: Anonymized after 26 months</li>
              </ul>
            </section>

            <section>
              <h2 className="text-2xl font-bold mb-4">6. Your Privacy Rights</h2>
              <p className="text-gray-700 mb-4">Depending on your location, you may have the following rights:</p>
              
              <h3 className="text-xl font-semibold mb-3">California Residents (CCPA/CPRA)</h3>
              <ul className="list-disc pl-6 space-y-2 text-gray-700 mb-4">
                <li><strong>Right to Know:</strong> Request information about data we collect, use, and share</li>
                <li><strong>Right to Delete:</strong> Request deletion of your personal information</li>
                <li><strong>Right to Correct:</strong> Request correction of inaccurate information</li>
                <li><strong>Right to Opt-Out:</strong> Opt out of sale or sharing (we do not sell data)</li>
                <li><strong>Right to Non-Discrimination:</strong> Not be discriminated against for exercising your rights</li>
              </ul>

              <h3 className="text-xl font-semibold mb-3">All Users</h3>
              <ul className="list-disc pl-6 space-y-2 text-gray-700">
                <li>Access your personal information we hold</li>
                <li>Correct inaccuracies in your data</li>
                <li>Request deletion of your information</li>
                <li>Opt out of marketing communications</li>
                <li>Object to processing of your information</li>
              </ul>

              <p className="text-gray-700 mt-4">
                To exercise these rights, contact us at <a href={`mailto:${COMPANY.email}`} className="text-primary hover:underline">{COMPANY.email}</a> or call {COMPANY.phone}. 
                We will respond within 30 days.
              </p>
            </section>

            <section>
              <h2 className="text-2xl font-bold mb-4">7. Data Security</h2>
              <p className="text-gray-700">
                We implement appropriate technical and organizational security measures to protect your personal information, including:
              </p>
              <ul className="list-disc pl-6 space-y-2 text-gray-700 mt-3">
                <li>HTTPS encryption for all data transmission</li>
                <li>Secure server infrastructure with access controls</li>
                <li>Regular security assessments and updates</li>
                <li>Employee training on data protection practices</li>
                <li>Incident response procedures</li>
              </ul>
              <p className="text-gray-700 mt-4">
                However, no method of transmission over the Internet is 100% secure. While we strive to protect your information, 
                we cannot guarantee absolute security.
              </p>
            </section>

            <section>
              <h2 className="text-2xl font-bold mb-4">8. Cookies and Tracking</h2>
              <p className="text-gray-700">
                We use cookies and similar technologies for analytics and functionality. See our{' '}
                <a href="/cookies" className="text-primary hover:underline">Cookie Policy</a> for detailed information 
                about the cookies we use and how to manage your preferences.
              </p>
            </section>

            <section>
              <h2 className="text-2xl font-bold mb-4">9. Third-Party Links</h2>
              <p className="text-gray-700">
                Our website may contain links to third-party websites. We are not responsible for the privacy practices 
                of these external sites. We encourage you to review their privacy policies before providing any information.
              </p>
            </section>

            <section>
              <h2 className="text-2xl font-bold mb-4">10. Children's Privacy</h2>
              <p className="text-gray-700">
                Our services are not directed to individuals under 18 years of age. We do not knowingly collect personal 
                information from children. If you believe we have collected information from a child, please contact us 
                immediately, and we will delete it.
              </p>
            </section>

            <section>
              <h2 className="text-2xl font-bold mb-4">11. Changes to This Policy</h2>
              <p className="text-gray-700">
                We may update this Privacy Policy from time to time. Changes will be posted on this page with an updated 
                &quot;Last Updated&quot; date. We encourage you to review this policy periodically. Continued use of our website 
                after changes constitutes acceptance of the updated policy.
              </p>
            </section>

            <section>
              <h2 className="text-2xl font-bold mb-4">12. Contact Us</h2>
              <p className="text-gray-700 mb-4">
                If you have questions about this Privacy Policy or wish to exercise your privacy rights, please contact us:
              </p>
              <div className="bg-gray-50 p-6 rounded-lg">
                <p className="text-gray-700"><strong>{COMPANY.legalName}</strong></p>
                <p className="text-gray-700">Email: <a href={`mailto:${COMPANY.email}`} className="text-primary hover:underline">{COMPANY.email}</a></p>
                <p className="text-gray-700">Phone: {COMPANY.phone}</p>
                {COMPANY.locations[0] && (
                  <>
                    <p className="text-gray-700 mt-3">Mailing Address:</p>
                    <p className="text-gray-700">{COMPANY.locations[0].address}</p>
                    <p className="text-gray-700">{COMPANY.locations[0].city}, {COMPANY.locations[0].state} {COMPANY.locations[0].zip}</p>
                  </>
                )}
              </div>
            </section>
          </div>
        </div>
      </div>
    </div>
  );
}
