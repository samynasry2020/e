import { generatePageMetadata } from '@/lib/metadata';
import { COMPANY, DISCLAIMERS } from '@/lib/constants';
import RFPForm from '@/components/RFPForm';

export const metadata = generatePageMetadata({
  title: 'Government Contracting',
  description: 'IT hardware solutions for federal, state, and local government agencies. NAICS 334111, 423430, 541512. RFP support and procurement assistance.',
  keywords: ['government IT', 'federal contracting', 'NAICS 334111', 'government procurement', 'RFP', 'agency IT'],
  path: '/government',
});

export default function GovernmentPage() {
  return (
    <>
      {/* Hero */}
      <section className="bg-gradient-to-br from-primary to-primary-dark text-white py-16">
        <div className="container">
          <div className="max-w-4xl mx-auto text-center">
            <h1 className="mb-6">Government Contracting Services</h1>
            <p className="text-xl text-blue-100 mb-6">
              IT Hardware Solutions for Federal, State, and Local Government Agencies
            </p>
            <div className="bg-white/10 backdrop-blur-sm rounded-lg p-4 text-sm">
              <p className="font-semibold mb-2">⚠️ Important Disclaimer</p>
              <p>{DISCLAIMERS.government}</p>
            </div>
          </div>
        </div>
      </section>

      {/* What We Do */}
      <section className="section">
        <div className="container">
          <div className="max-w-4xl mx-auto">
            <h2 className="text-center mb-8">What We Do for Government Agencies</h2>
            <div className="grid md:grid-cols-2 gap-6">
              <div className="card">
                <div className="text-3xl mb-3">🖥️</div>
                <h3 className="font-bold mb-2">IT Hardware Supply</h3>
                <p className="text-gray-600">
                  Enterprise servers, workstations, storage, networking equipment, and complete data center infrastructure
                </p>
              </div>

              <div className="card">
                <div className="text-3xl mb-3">🏗️</div>
                <h3 className="font-bold mb-2">Infrastructure Projects</h3>
                <p className="text-gray-600">
                  Data center builds, modernization projects, and large-scale IT deployments
                </p>
              </div>

              <div className="card">
                <div className="text-3xl mb-3">🎯</div>
                <h3 className="font-bold mb-2">Custom Solutions</h3>
                <p className="text-gray-600">
                  Tailored configurations to meet agency-specific requirements and security standards
                </p>
              </div>

              <div className="card">
                <div className="text-3xl mb-3">📋</div>
                <h3 className="font-bold mb-2">RFP Response Support</h3>
                <p className="text-gray-600">
                  Fast turnaround on quotes, technical specifications, and bid submissions
                </p>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* NAICS Codes */}
      <section className="section bg-gray-50">
        <div className="container">
          <div className="max-w-4xl mx-auto">
            <h2 className="text-center mb-8">NAICS Codes</h2>
            <p className="text-center text-gray-600 mb-8">
              We operate under the following North American Industry Classification System (NAICS) codes
            </p>
            <div className="space-y-4">
              {COMPANY.naicsCodes.map((naics) => (
                <div key={naics.code} className="card flex items-start justify-between">
                  <div>
                    <div className="font-bold text-primary text-lg mb-1">NAICS {naics.code}</div>
                    <p className="text-gray-700">{naics.description}</p>
                  </div>
                </div>
              ))}
            </div>

            <div className="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-6">
              <h3 className="font-bold mb-3">PSC Codes (Product Service Codes)</h3>
              <p className="text-gray-700 mb-3">
                We supply products and services under the following Federal Supply Classification families:
              </p>
              <div className="flex flex-wrap gap-3">
                {COMPANY.pscCodes.map((code) => (
                  <span key={code} className="bg-white px-4 py-2 rounded-lg font-semibold text-primary border border-blue-200">
                    PSC {code}
                  </span>
                ))}
              </div>
              <p className="text-sm text-gray-600 mt-4">
                (ADP Software, ADP Data Processing Services, ADP Systems Development, Communications Equipment, IT Professional Services)
              </p>
            </div>
          </div>
        </div>
      </section>

      {/* Registration & Certifications */}
      <section className="section">
        <div className="container">
          <div className="max-w-4xl mx-auto">
            <h2 className="text-center mb-8">Business Registrations</h2>
            
            <div className="grid md:grid-cols-3 gap-6 mb-8">
              <div className="card text-center">
                <div className="font-bold text-lg mb-2">UEI</div>
                <div className="text-gray-700 font-mono text-sm">{COMPANY.uei}</div>
              </div>
              <div className="card text-center">
                <div className="font-bold text-lg mb-2">CAGE Code</div>
                <div className="text-gray-700 font-mono text-sm">{COMPANY.cage}</div>
              </div>
              <div className="card text-center">
                <div className="font-bold text-lg mb-2">DUNS</div>
                <div className="text-gray-700 font-mono text-sm">{COMPANY.duns}</div>
              </div>
            </div>

            <div className="card">
              <h3 className="font-bold mb-4">Small Business Certifications & Set-Asides</h3>
              {COMPANY.certifications.length > 0 ? (
                <div className="space-y-3">
                  {COMPANY.certifications.map((cert, index) => (
                    <div key={index} className="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                      <div>
                        <div className="font-semibold">{cert.name}</div>
                        {cert.id && <div className="text-sm text-gray-600">ID: {cert.id}</div>}
                      </div>
                      <span className={`px-3 py-1 rounded-full text-sm font-semibold ${
                        cert.status === 'Active' ? 'bg-green-100 text-green-700' :
                        cert.status === 'Pending' ? 'bg-yellow-100 text-yellow-700' :
                        'bg-blue-100 text-blue-700'
                      }`}>
                        {cert.status}
                      </span>
                    </div>
                  ))}
                </div>
              ) : (
                <div className="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                  <p className="text-sm text-gray-700">
                    We are currently pursuing small business certifications including WOSB (Women-Owned Small Business) 
                    and state-level certifications. We will update this section with official certification details 
                    upon approval. We maintain transparency and do not claim certifications we do not hold.
                  </p>
                </div>
              )}
            </div>
          </div>
        </div>
      </section>

      {/* Capability Statement */}
      <section className="section bg-gray-50">
        <div className="container">
          <div className="max-w-4xl mx-auto text-center">
            <h2 className="mb-4">Capability Statement</h2>
            <p className="text-gray-600 mb-6">
              Download our one-page capability statement for your procurement file
            </p>
            <a
              href="/capability-statement.pdf"
              download
              className="btn btn-primary btn-large"
              target="_blank"
              rel="noopener noreferrer"
            >
              📄 Download Capability Statement (PDF)
            </a>
            <p className="text-sm text-gray-500 mt-4">
              Last updated: {new Date().toLocaleDateString('en-US', { month: 'long', year: 'numeric' })}
            </p>
          </div>
        </div>
      </section>

      {/* RFP Form */}
      <section className="section">
        <div className="container">
          <div className="max-w-4xl mx-auto">
            <h2 className="text-center mb-4">Submit RFP / Bid Request</h2>
            <p className="text-center text-gray-600 mb-8">
              We respond to RFP and bid requests within 4 hours during business days
            </p>
            <div className="card">
              <RFPForm />
            </div>
          </div>
        </div>
      </section>

      {/* Past Performance */}
      <section className="section bg-gray-50">
        <div className="container">
          <div className="max-w-4xl mx-auto">
            <h2 className="text-center mb-8">Areas of Expertise</h2>
            <div className="grid md:grid-cols-2 gap-6">
              <div>
                <h3 className="font-bold mb-3">🏛️ Agency Types We Serve</h3>
                <ul className="space-y-2 text-gray-700">
                  <li>✓ Federal civilian agencies</li>
                  <li>✓ Defense and intelligence (where authorized)</li>
                  <li>✓ State government departments</li>
                  <li>✓ Local government and municipalities</li>
                  <li>✓ Educational institutions</li>
                  <li>✓ Healthcare facilities</li>
                </ul>
              </div>

              <div>
                <h3 className="font-bold mb-3">⚙️ Technical Capabilities</h3>
                <ul className="space-y-2 text-gray-700">
                  <li>✓ High-security configurations (FIPS, STIG)</li>
                  <li>✓ Large-scale deployments (100+ units)</li>
                  <li>✓ Custom imaging and configuration</li>
                  <li>✓ Multi-site delivery coordination</li>
                  <li>✓ On-site installation services</li>
                  <li>✓ Asset tagging and tracking</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Contact CTA */}
      <section className="section bg-primary text-white">
        <div className="container text-center">
          <h2 className="mb-4">Ready to Work Together?</h2>
          <p className="text-xl mb-8 text-blue-100 max-w-2xl mx-auto">
            Contact us to discuss your agency's IT requirements or to request a quote
          </p>
          <div className="flex flex-col sm:flex-row gap-4 justify-center">
            <a href={`mailto:${COMPANY.email}`} className="btn btn-large" style={{ backgroundColor: 'white', color: 'var(--color-primary)' }}>
              Email Us
            </a>
            <a href={`tel:${COMPANY.phoneRaw}`} className="btn btn-large btn-secondary" style={{ borderColor: 'white', color: 'white' }}>
              📞 {COMPANY.phone}
            </a>
          </div>
        </div>
      </section>
    </>
  );
}
