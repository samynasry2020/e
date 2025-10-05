import { generatePageMetadata } from '@/lib/metadata';
import { COMPANY } from '@/lib/constants';
import ContactForm from '@/components/ContactForm';

export const metadata = generatePageMetadata({
  title: 'Contact Us',
  description: 'Get in touch with PU Build for IT hardware quotes, technical consultation, and government contracting inquiries.',
  path: '/contact',
});

export default function ContactPage() {
  return (
    <>
      {/* Hero */}
      <section className="bg-gradient-to-br from-primary to-primary-dark text-white py-16">
        <div className="container">
          <div className="max-w-4xl mx-auto text-center">
            <h1 className="mb-6">Contact Us</h1>
            <p className="text-xl text-blue-100">
              Get in touch with our team for quotes, technical consultation, or general inquiries
            </p>
          </div>
        </div>
      </section>

      {/* Contact Methods */}
      <section className="section bg-gray-50">
        <div className="container">
          <div className="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
            <div className="card text-center">
              <div className="text-4xl mb-4">📞</div>
              <h3 className="font-bold mb-3">Phone</h3>
              <a
                href={`tel:${COMPANY.phoneRaw}`}
                className="text-primary hover:underline text-lg font-semibold block mb-2"
              >
                {COMPANY.phone}
              </a>
              <p className="text-sm text-gray-600">{COMPANY.hours.weekdays}</p>
              <p className="text-sm text-gray-600">{COMPANY.hours.weekend}</p>
              <p className="text-xs text-gray-500 mt-2">{COMPANY.hours.support}</p>
            </div>

            <div className="card text-center">
              <div className="text-4xl mb-4">✉️</div>
              <h3 className="font-bold mb-3">Email</h3>
              <a
                href={`mailto:${COMPANY.email}`}
                className="text-primary hover:underline block mb-2"
              >
                {COMPANY.email}
              </a>
              <a
                href={`mailto:${COMPANY.supportEmail}`}
                className="text-primary hover:underline block mb-3"
              >
                {COMPANY.supportEmail}
              </a>
              <p className="text-sm text-gray-600">Response within {COMPANY.responseSLA}</p>
            </div>

            <div className="card text-center">
              <div className="text-4xl mb-4">🏢</div>
              <h3 className="font-bold mb-3">Office</h3>
              {COMPANY.locations[0] && (
                <>
                  <p className="text-sm text-gray-700">{COMPANY.locations[0].address}</p>
                  <p className="text-sm text-gray-700">
                    {COMPANY.locations[0].city}, {COMPANY.locations[0].state} {COMPANY.locations[0].zip}
                  </p>
                  <p className="text-sm text-gray-600 mt-2">{COMPANY.locations[0].country}</p>
                </>
              )}
            </div>
          </div>
        </div>
      </section>

      {/* Contact Form */}
      <section className="section">
        <div className="container">
          <div className="max-w-3xl mx-auto">
            <div className="text-center mb-8">
              <h2 className="mb-4">Send Us a Message</h2>
              <p className="text-gray-600">
                Fill out the form below and we'll get back to you within 24 hours
              </p>
            </div>

            <div className="card">
              <ContactForm />
            </div>
          </div>
        </div>
      </section>

      {/* Government RFP */}
      <section className="section bg-gray-50">
        <div className="container">
          <div className="max-w-3xl mx-auto text-center">
            <div className="card bg-blue-50 border-blue-200">
              <h3 className="text-xl font-bold mb-3">📋 Government RFP / Bid Request?</h3>
              <p className="text-gray-700 mb-4">
                For government procurement and RFP submissions, please use our dedicated bid support form 
                for faster response (4-hour turnaround during business days).
              </p>
              <a href="/government" className="btn btn-primary">
                Go to Government Contracting Page
              </a>
            </div>
          </div>
        </div>
      </section>

      {/* Map / Additional Info */}
      <section className="section">
        <div className="container">
          <div className="max-w-4xl mx-auto">
            <h2 className="text-center mb-8">Additional Information</h2>
            <div className="grid md:grid-cols-2 gap-8">
              <div className="card">
                <h3 className="font-bold mb-3">🕐 Business Hours</h3>
                <div className="space-y-2 text-gray-700">
                  <p><strong>Monday - Friday:</strong> 8:00 AM - 6:00 PM PT</p>
                  <p><strong>Saturday - Sunday:</strong> Closed</p>
                  <p className="text-sm text-gray-600 mt-3">
                    Emergency support available 24/7 for existing customers with support contracts.
                  </p>
                </div>
              </div>

              <div className="card">
                <h3 className="font-bold mb-3">⚡ Response Times</h3>
                <div className="space-y-2 text-gray-700">
                  <p><strong>General Inquiries:</strong> Within 24 hours</p>
                  <p><strong>Quote Requests:</strong> Within 24 hours</p>
                  <p><strong>RFP / Bid Requests:</strong> Within 4 hours</p>
                  <p className="text-sm text-gray-600 mt-3">
                    Response times are for business days. We'll acknowledge receipt immediately and provide 
                    a full response within the stated timeframe.
                  </p>
                </div>
              </div>

              <div className="card">
                <h3 className="font-bold mb-3">📦 Shipping & Delivery</h3>
                <div className="space-y-2 text-gray-700 text-sm">
                  <p>✓ Nationwide delivery available</p>
                  <p>✓ White-glove on-site delivery and installation</p>
                  <p>✓ Multi-site coordination</p>
                  <p>✓ Freight and expedited shipping options</p>
                </div>
              </div>

              <div className="card">
                <h3 className="font-bold mb-3">🔒 Privacy & Security</h3>
                <div className="space-y-2 text-gray-700 text-sm">
                  <p>✓ Your information is never sold or shared</p>
                  <p>✓ Secure form submission (HTTPS)</p>
                  <p>✓ CCPA/CPRA compliant data handling</p>
                  <p>✓ See our <a href="/privacy" className="text-primary hover:underline">Privacy Policy</a></p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>
    </>
  );
}
