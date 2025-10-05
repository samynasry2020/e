import { generatePageMetadata } from '@/lib/metadata';
import { COMPANY } from '@/lib/constants';

export const metadata = generatePageMetadata({
  title: 'Terms of Use',
  description: 'Terms and conditions for using the PU Build website and services.',
  path: '/terms',
});

export default function TermsPage() {
  const lastUpdated = 'January 1, 2025';

  return (
    <div className="section">
      <div className="container">
        <div className="max-w-4xl mx-auto">
          <h1 className="mb-4">Terms of Use</h1>
          <p className="text-gray-600 mb-8">Last Updated: {lastUpdated}</p>

          <div className="prose prose-lg max-w-none space-y-8">
            <section>
              <h2 className="text-2xl font-bold mb-4">1. Acceptance of Terms</h2>
              <p className="text-gray-700">
                By accessing and using the website {COMPANY.domain} (the &quot;Website&quot;) and services provided by{' '}
                {COMPANY.legalName} (&quot;{COMPANY.name},&quot; &quot;we,&quot; &quot;our,&quot; or &quot;us&quot;), you agree to be bound by these 
                Terms of Use. If you do not agree to these terms, please do not use our Website or services.
              </p>
            </section>

            <section>
              <h2 className="text-2xl font-bold mb-4">2. Use of Website</h2>
              
              <h3 className="text-xl font-semibold mb-3">2.1 Permitted Use</h3>
              <p className="text-gray-700 mb-3">You may use the Website for lawful purposes only, including:</p>
              <ul className="list-disc pl-6 space-y-2 text-gray-700">
                <li>Viewing product and service information</li>
                <li>Requesting quotes and submitting inquiries</li>
                <li>Accessing resources and support materials</li>
              </ul>

              <h3 className="text-xl font-semibold mb-3 mt-6">2.2 Prohibited Conduct</h3>
              <p className="text-gray-700 mb-3">You agree not to:</p>
              <ul className="list-disc pl-6 space-y-2 text-gray-700">
                <li>Use the Website for any unlawful purpose or to violate any laws</li>
                <li>Attempt to gain unauthorized access to our systems or networks</li>
                <li>Interfere with or disrupt the Website or servers</li>
                <li>Transmit viruses, malware, or other harmful code</li>
                <li>Scrape, crawl, or harvest data from the Website without permission</li>
                <li>Impersonate another person or entity</li>
                <li>Use the Website to spam, phish, or send unsolicited communications</li>
              </ul>
            </section>

            <section>
              <h2 className="text-2xl font-bold mb-4">3. Intellectual Property</h2>
              
              <h3 className="text-xl font-semibold mb-3">3.1 Our Content</h3>
              <p className="text-gray-700">
                All content on this Website, including text, graphics, logos, images, software, and other materials, 
                is the property of {COMPANY.name} or its licensors and is protected by copyright, trademark, and other 
                intellectual property laws. You may not reproduce, distribute, modify, or create derivative works without 
                our express written permission.
              </p>

              <h3 className="text-xl font-semibold mb-3 mt-6">3.2 Trademarks</h3>
              <p className="text-gray-700">
                {COMPANY.name} and our logo are trademarks of {COMPANY.legalName}. Third-party trademarks mentioned on 
                this Website are the property of their respective owners. References to third-party products are for 
                compatibility information only and do not imply endorsement or affiliation.
              </p>

              <h3 className="text-xl font-semibold mb-3 mt-6">3.3 User Submissions</h3>
              <p className="text-gray-700">
                By submitting content (including feedback, suggestions, or ideas) to us, you grant {COMPANY.name} a 
                non-exclusive, royalty-free, perpetual license to use, reproduce, and incorporate such content for 
                business purposes.
              </p>
            </section>

            <section>
              <h2 className="text-2xl font-bold mb-4">4. Product Information and Pricing</h2>
              <p className="text-gray-700">
                Product descriptions, specifications, and pricing information on this Website are subject to change 
                without notice. While we strive for accuracy, we do not warrant that product information is complete, 
                current, or error-free. All quotes and pricing are subject to confirmation and availability.
              </p>
              <p className="text-gray-700 mt-4">
                <strong>No GSA or Contract Pricing:</strong> Unless we hold a GSA Schedule or other government contract 
                vehicle, pricing displayed is not contract pricing. Contact us for current quotes.
              </p>
            </section>

            <section>
              <h2 className="text-2xl font-bold mb-4">5. Orders and Quotes</h2>
              <p className="text-gray-700">
                Quote requests and inquiries submitted through the Website do not constitute binding offers or orders. 
                A binding agreement is formed only when we accept your purchase order or you accept our written quote. 
                We reserve the right to refuse or cancel orders at our discretion.
              </p>
            </section>

            <section>
              <h2 className="text-2xl font-bold mb-4">6. Disclaimers and Warranties</h2>
              
              <h3 className="text-xl font-semibold mb-3">6.1 Website &quot;As Is&quot;</h3>
              <p className="text-gray-700">
                THE WEBSITE IS PROVIDED &quot;AS IS&quot; AND &quot;AS AVAILABLE&quot; WITHOUT WARRANTIES OF ANY KIND, EITHER EXPRESS OR IMPLIED, 
                INCLUDING BUT NOT LIMITED TO WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE, OR NON-INFRINGEMENT.
              </p>

              <h3 className="text-xl font-semibold mb-3 mt-6">6.2 Product Warranties</h3>
              <p className="text-gray-700">
                Products are covered by manufacturer warranties. {COMPANY.name} acts as a reseller and facilitates warranty 
                service but does not provide its own product warranties unless explicitly stated in writing. Refer to 
                manufacturer documentation for warranty terms.
              </p>

              <h3 className="text-xl font-semibold mb-3 mt-6">6.3 No Government Endorsement</h3>
              <p className="text-gray-700">
                {COMPANY.name} is an independent supplier. We are not endorsed by, affiliated with, or an official contractor 
                of the U.S. Government or any government agency unless we hold specific contract vehicles. No government 
                seals, logos, or endorsements are implied.
              </p>
            </section>

            <section>
              <h2 className="text-2xl font-bold mb-4">7. Limitation of Liability</h2>
              <p className="text-gray-700">
                TO THE MAXIMUM EXTENT PERMITTED BY LAW, {COMPANY.name.toUpperCase()} SHALL NOT BE LIABLE FOR ANY INDIRECT, 
                INCIDENTAL, SPECIAL, CONSEQUENTIAL, OR PUNITIVE DAMAGES, OR ANY LOSS OF PROFITS OR REVENUES, WHETHER INCURRED 
                DIRECTLY OR INDIRECTLY, OR ANY LOSS OF DATA, USE, GOODWILL, OR OTHER INTANGIBLE LOSSES ARISING FROM:
              </p>
              <ul className="list-disc pl-6 space-y-2 text-gray-700 mt-3">
                <li>Your use of or inability to use the Website or services</li>
                <li>Unauthorized access to or alteration of your data</li>
                <li>Any content or conduct of third parties on the Website</li>
                <li>Any other matter relating to the Website or services</li>
              </ul>
              <p className="text-gray-700 mt-4">
                Our total liability to you for all claims shall not exceed the amount you paid us in the 12 months 
                preceding the claim, or $100, whichever is greater.
              </p>
            </section>

            <section>
              <h2 className="text-2xl font-bold mb-4">8. Indemnification</h2>
              <p className="text-gray-700">
                You agree to indemnify, defend, and hold harmless {COMPANY.name}, its officers, directors, employees, 
                and agents from any claims, liabilities, damages, losses, and expenses (including attorneys' fees) 
                arising from your use of the Website, violation of these Terms, or infringement of any third-party rights.
              </p>
            </section>

            <section>
              <h2 className="text-2xl font-bold mb-4">9. Export Controls</h2>
              <p className="text-gray-700">
                Certain products may be subject to U.S. export controls under the Export Administration Regulations (EAR) 
                or International Traffic in Arms Regulations (ITAR). Purchasers are responsible for compliance with all 
                applicable export control laws and regulations. {COMPANY.name} makes no warranties regarding export compliance.
              </p>
            </section>

            <section>
              <h2 className="text-2xl font-bold mb-4">10. Privacy</h2>
              <p className="text-gray-700">
                Your use of the Website is also governed by our{' '}
                <a href="/privacy" className="text-primary hover:underline">Privacy Policy</a>. Please review our Privacy 
                Policy to understand how we collect, use, and protect your information.
              </p>
            </section>

            <section>
              <h2 className="text-2xl font-bold mb-4">11. Governing Law and Jurisdiction</h2>
              <p className="text-gray-700">
                These Terms shall be governed by and construed in accordance with the laws of the State of California, 
                without regard to its conflict of law provisions. Any disputes arising from these Terms or your use of 
                the Website shall be resolved in the state or federal courts located in California, and you consent to 
                the exclusive jurisdiction of such courts.
              </p>
            </section>

            <section>
              <h2 className="text-2xl font-bold mb-4">12. Modifications to Terms</h2>
              <p className="text-gray-700">
                We reserve the right to modify these Terms at any time. Changes will be posted on this page with an 
                updated &quot;Last Updated&quot; date. Your continued use of the Website after changes constitutes acceptance 
                of the modified Terms.
              </p>
            </section>

            <section>
              <h2 className="text-2xl font-bold mb-4">13. Severability</h2>
              <p className="text-gray-700">
                If any provision of these Terms is found to be invalid or unenforceable, the remaining provisions shall 
                remain in full force and effect.
              </p>
            </section>

            <section>
              <h2 className="text-2xl font-bold mb-4">14. Contact Us</h2>
              <p className="text-gray-700 mb-4">
                If you have questions about these Terms, please contact us:
              </p>
              <div className="bg-gray-50 p-6 rounded-lg">
                <p className="text-gray-700"><strong>{COMPANY.legalName}</strong></p>
                <p className="text-gray-700">Email: <a href={`mailto:${COMPANY.email}`} className="text-primary hover:underline">{COMPANY.email}</a></p>
                <p className="text-gray-700">Phone: {COMPANY.phone}</p>
                {COMPANY.locations[0] && (
                  <>
                    <p className="text-gray-700 mt-3">Address:</p>
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
