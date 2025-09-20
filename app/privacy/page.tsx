export default function PrivacyPage() {
  return (
    <div className="min-h-screen bg-white py-20">
      <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 className="text-4xl font-bold text-secondary-900 mb-8">
          Privacy Policy
        </h1>
        
        <div className="prose prose-lg max-w-none">
          <p className="text-secondary-600 mb-8">
            Last updated: January 2025
          </p>

          <section className="mb-8">
            <h2 className="text-2xl font-bold text-secondary-900 mb-4">
              Government Data Protection
            </h2>
            <p className="text-secondary-700 mb-4">
              PUBUILD TECHNOLOGIES INC. is committed to protecting the privacy and security of all 
              government data and information. We adhere to the highest standards of data protection 
              as required by federal regulations and government contracts.
            </p>
          </section>

          <section className="mb-8">
            <h2 className="text-2xl font-bold text-secondary-900 mb-4">
              Information Collection
            </h2>
            <p className="text-secondary-700 mb-4">
              We collect only the information necessary to provide government server solutions:
            </p>
            <ul className="list-disc pl-6 text-secondary-700 mb-4">
              <li>Government contact information for authorized personnel</li>
              <li>Technical requirements and specifications</li>
              <li>Security clearance information where applicable</li>
              <li>Contract and project-related data</li>
            </ul>
          </section>

          <section className="mb-8">
            <h2 className="text-2xl font-bold text-secondary-900 mb-4">
              Data Security
            </h2>
            <p className="text-secondary-700 mb-4">
              All data is protected using government-approved security measures:
            </p>
            <ul className="list-disc pl-6 text-secondary-700 mb-4">
              <li>FIPS 140-2 Level 4 encryption</li>
              <li>FedRAMP High security controls</li>
              <li>Access limited to cleared personnel only</li>
              <li>Regular security audits and compliance reviews</li>
            </ul>
          </section>

          <section className="mb-8">
            <h2 className="text-2xl font-bold text-secondary-900 mb-4">
              Government Compliance
            </h2>
            <p className="text-secondary-700 mb-4">
              Our privacy practices comply with all applicable federal regulations including:
            </p>
            <ul className="list-disc pl-6 text-secondary-700 mb-4">
              <li>Federal Information Security Management Act (FISMA)</li>
              <li>Privacy Act of 1974</li>
              <li>Federal Acquisition Regulation (FAR)</li>
              <li>Defense Federal Acquisition Regulation (DFARS)</li>
            </ul>
          </section>

          <section className="mb-8">
            <h2 className="text-2xl font-bold text-secondary-900 mb-4">
              Contact Information
            </h2>
            <p className="text-secondary-700">
              For privacy-related inquiries, contact our Privacy Officer at:
              <br />
              Email: privacy@pubuild.com
              <br />
              Phone: +1 (555) 123-4567
            </p>
          </section>
        </div>
      </div>
    </div>
  )
}