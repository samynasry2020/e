export default function TermsPage() {
  return (
    <div className="min-h-screen bg-white py-20">
      <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <h1 className="text-4xl font-bold text-secondary-900 mb-8">
          Terms of Service
        </h1>
        
        <div className="prose prose-lg max-w-none">
          <p className="text-secondary-600 mb-8">
            Last updated: January 2025
          </p>

          <section className="mb-8">
            <h2 className="text-2xl font-bold text-secondary-900 mb-4">
              Government Services Agreement
            </h2>
            <p className="text-secondary-700 mb-4">
              These terms govern the provision of server solutions and related services 
              exclusively to United States Government agencies and authorized contractors.
            </p>
          </section>

          <section className="mb-8">
            <h2 className="text-2xl font-bold text-secondary-900 mb-4">
              Service Scope
            </h2>
            <p className="text-secondary-700 mb-4">
              PUBUILD TECHNOLOGIES INC. provides:
            </p>
            <ul className="list-disc pl-6 text-secondary-700 mb-4">
              <li>Custom server manufacturing and assembly</li>
              <li>Professional installation and configuration</li>
              <li>Security implementation and hardening</li>
              <li>24/7 technical support for government clients</li>
              <li>Maintenance and upgrade services</li>
            </ul>
          </section>

          <section className="mb-8">
            <h2 className="text-2xl font-bold text-secondary-900 mb-4">
              Government Exclusive Policy
            </h2>
            <p className="text-secondary-700 mb-4">
              Our services are available exclusively to:
            </p>
            <ul className="list-disc pl-6 text-secondary-700 mb-4">
              <li>Federal government agencies</li>
              <li>State and local government entities</li>
              <li>Authorized government contractors</li>
              <li>Military and defense organizations</li>
            </ul>
          </section>

          <section className="mb-8">
            <h2 className="text-2xl font-bold text-secondary-900 mb-4">
              Security Requirements
            </h2>
            <p className="text-secondary-700 mb-4">
              All personnel working on government projects maintain appropriate security clearances 
              and follow strict security protocols as required by federal regulations.
            </p>
          </section>

          <section className="mb-8">
            <h2 className="text-2xl font-bold text-secondary-900 mb-4">
              Contract Terms
            </h2>
            <p className="text-secondary-700 mb-4">
              Specific terms and conditions are governed by individual government contracts 
              and purchase orders, which take precedence over these general terms.
            </p>
          </section>

          <section className="mb-8">
            <h2 className="text-2xl font-bold text-secondary-900 mb-4">
              Contact Information
            </h2>
            <p className="text-secondary-700">
              For questions regarding these terms, contact:
              <br />
              Email: legal@pubuild.com
              <br />
              Phone: +1 (555) 123-4567
            </p>
          </section>
        </div>
      </div>
    </div>
  )
}