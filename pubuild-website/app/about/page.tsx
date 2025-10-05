import Link from 'next/link';
import { generatePageMetadata } from '@/lib/metadata';
import { COMPANY } from '@/lib/constants';

export const metadata = generatePageMetadata({
  title: 'About Us',
  description: 'Learn about PU Build - your trusted partner for enterprise IT hardware and government contracting services.',
  path: '/about',
});

export default function AboutPage() {
  return (
    <>
      {/* Hero */}
      <section className="bg-gradient-to-br from-gray-900 to-gray-700 text-white py-16">
        <div className="container">
          <div className="max-w-4xl mx-auto text-center">
            <h1 className="mb-6">About PU Build</h1>
            <p className="text-xl text-gray-200">
              Enterprise IT Solutions Partner for Government and Commercial Organizations
            </p>
          </div>
        </div>
      </section>

      {/* Mission */}
      <section className="section">
        <div className="container">
          <div className="max-w-4xl mx-auto">
            <h2 className="text-center mb-8">Our Mission</h2>
            <div className="card text-center">
              <p className="text-xl text-gray-700 leading-relaxed">
                To provide reliable, high-performance IT infrastructure solutions that empower organizations 
                to achieve their missions. We combine technical expertise with responsive service to deliver 
                value at every stage of the procurement process.
              </p>
            </div>
          </div>
        </div>
      </section>

      {/* What We Do */}
      <section className="section bg-gray-50">
        <div className="container">
          <div className="max-w-4xl mx-auto">
            <h2 className="text-center mb-12">What We Do</h2>
            <div className="grid md:grid-cols-2 gap-8">
              <div className="card">
                <div className="text-4xl mb-4">🖥️</div>
                <h3 className="font-bold text-xl mb-3">IT Hardware Supply</h3>
                <p className="text-gray-600 mb-4">
                  We provide enterprise-grade servers, workstations, storage, networking equipment, and GPUs 
                  from leading manufacturers. Every system is configured, tested, and ready for deployment.
                </p>
                <ul className="text-sm text-gray-600 space-y-1">
                  <li>✓ Custom configurations</li>
                  <li>✓ Quality assurance testing</li>
                  <li>✓ Manufacturer warranties</li>
                  <li>✓ Technical documentation</li>
                </ul>
              </div>

              <div className="card">
                <div className="text-4xl mb-4">🏛️</div>
                <h3 className="font-bold text-xl mb-3">Government Contracting</h3>
                <p className="text-gray-600 mb-4">
                  Experienced with federal, state, and local procurement processes. We understand agency 
                  requirements and respond quickly to RFPs and bid requests.
                </p>
                <ul className="text-sm text-gray-600 space-y-1">
                  <li>✓ NAICS 334111, 423430, 541512</li>
                  <li>✓ Fast RFP response (4 hours)</li>
                  <li>✓ Compliance documentation</li>
                  <li>✓ Security configurations</li>
                </ul>
              </div>

              <div className="card">
                <div className="text-4xl mb-4">🎯</div>
                <h3 className="font-bold text-xl mb-3">Solution Design</h3>
                <p className="text-gray-600 mb-4">
                  Our team provides technical consultation to help you select the right infrastructure 
                  for your workload requirements and budget.
                </p>
                <ul className="text-sm text-gray-600 space-y-1">
                  <li>✓ Capacity planning</li>
                  <li>✓ Architecture design</li>
                  <li>✓ Performance optimization</li>
                  <li>✓ Cost analysis</li>
                </ul>
              </div>

              <div className="card">
                <div className="text-4xl mb-4">🚀</div>
                <h3 className="font-bold text-xl mb-3">Deployment Services</h3>
                <p className="text-gray-600 mb-4">
                  From single workstations to complete data centers, we handle logistics, installation, 
                  and commissioning to ensure successful deployments.
                </p>
                <ul className="text-sm text-gray-600 space-y-1">
                  <li>✓ On-site installation</li>
                  <li>✓ Rack integration</li>
                  <li>✓ System commissioning</li>
                  <li>✓ Training and handoff</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Values */}
      <section className="section">
        <div className="container">
          <div className="max-w-4xl mx-auto">
            <h2 className="text-center mb-12">Our Values</h2>
            <div className="space-y-6">
              <div className="flex items-start">
                <div className="text-3xl mr-4">💯</div>
                <div>
                  <h3 className="font-bold text-lg mb-2">Transparency</h3>
                  <p className="text-gray-600">
                    We maintain complete honesty about our capabilities, certifications, and limitations. 
                    No false claims, no misrepresentation.
                  </p>
                </div>
              </div>

              <div className="flex items-start">
                <div className="text-3xl mr-4">⚡</div>
                <div>
                  <h3 className="font-bold text-lg mb-2">Responsiveness</h3>
                  <p className="text-gray-600">
                    Fast turnaround on quotes and inquiries. We respect your deadlines and procurement timelines.
                  </p>
                </div>
              </div>

              <div className="flex items-start">
                <div className="text-3xl mr-4">🎯</div>
                <div>
                  <h3 className="font-bold text-lg mb-2">Expertise</h3>
                  <p className="text-gray-600">
                    Deep technical knowledge across enterprise IT infrastructure, from component selection 
                    to system integration.
                  </p>
                </div>
              </div>

              <div className="flex items-start">
                <div className="text-3xl mr-4">🤝</div>
                <div>
                  <h3 className="font-bold text-lg mb-2">Partnership</h3>
                  <p className="text-gray-600">
                    We view every customer relationship as a long-term partnership. Your success is our success.
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Locations */}
      <section className="section bg-gray-50">
        <div className="container">
          <div className="max-w-4xl mx-auto">
            <h2 className="text-center mb-8">Contact Information</h2>
            <div className="grid md:grid-cols-2 gap-8">
              {COMPANY.locations.map((location, index) => (
                <div key={index} className="card">
                  <h3 className="font-bold mb-3">{location.name}</h3>
                  <div className="space-y-2 text-gray-700">
                    <p>{location.address}</p>
                    <p>{location.city}, {location.state} {location.zip}</p>
                    <p>{location.country}</p>
                  </div>
                </div>
              ))}

              <div className="card">
                <h3 className="font-bold mb-3">Get in Touch</h3>
                <div className="space-y-3 text-gray-700">
                  <div>
                    <div className="text-sm text-gray-500">Phone</div>
                    <a href={`tel:${COMPANY.phoneRaw}`} className="text-primary hover:underline font-semibold">
                      {COMPANY.phone}
                    </a>
                  </div>
                  <div>
                    <div className="text-sm text-gray-500">Email</div>
                    <a href={`mailto:${COMPANY.email}`} className="text-primary hover:underline">
                      {COMPANY.email}
                    </a>
                  </div>
                  <div>
                    <div className="text-sm text-gray-500">Business Hours</div>
                    <p className="text-sm">{COMPANY.hours.weekdays}</p>
                    <p className="text-sm">{COMPANY.hours.weekend}</p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* CTA */}
      <section className="section bg-primary text-white">
        <div className="container text-center">
          <h2 className="mb-4">Let's Work Together</h2>
          <p className="text-xl mb-8 text-blue-100 max-w-2xl mx-auto">
            Ready to discuss your IT infrastructure needs? Get in touch with our team
          </p>
          <div className="flex flex-col sm:flex-row gap-4 justify-center">
            <Link href="/contact" className="btn btn-large" style={{ backgroundColor: 'white', color: 'var(--color-primary)' }}>
              Contact Us
            </Link>
            <Link href="/government" className="btn btn-large btn-secondary" style={{ borderColor: 'white', color: 'white' }}>
              Government Contracting
            </Link>
          </div>
        </div>
      </section>
    </>
  );
}
