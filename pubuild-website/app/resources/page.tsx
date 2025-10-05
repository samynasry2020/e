import Link from 'next/link';
import { generatePageMetadata } from '@/lib/metadata';
import { generateFAQSchema } from '@/lib/schema';
import faqsData from '@/content/faqs.json';
import { FAQItem } from '@/lib/types';

const faqs = faqsData as FAQItem[];

export const metadata = generatePageMetadata({
  title: 'Resources',
  description: 'Helpful resources, guides, and frequently asked questions about our IT solutions and government contracting services.',
  path: '/resources',
});

export default function ResourcesPage() {
  const faqSchema = generateFAQSchema(faqs);

  const categories = ['Government', 'Ordering', 'Support', 'Compliance', 'General'];

  return (
    <>
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(faqSchema) }}
      />

      {/* Hero */}
      <section className="bg-gradient-to-br from-gray-900 to-gray-700 text-white py-16">
        <div className="container">
          <div className="max-w-4xl mx-auto text-center">
            <h1 className="mb-6">Resources & Support</h1>
            <p className="text-xl text-gray-200">
              Helpful information about our products, services, and procurement process
            </p>
          </div>
        </div>
      </section>

      {/* Quick Links */}
      <section className="section bg-gray-50">
        <div className="container">
          <h2 className="text-center mb-12">Quick Links</h2>
          <div className="grid md:grid-cols-3 gap-6 max-w-4xl mx-auto">
            <Link href="/government" className="card hover:border-primary group">
              <div className="text-3xl mb-3">📋</div>
              <h3 className="font-bold mb-2 group-hover:text-primary transition-colors">
                Government Contracting Guide
              </h3>
              <p className="text-sm text-gray-600">
                NAICS codes, certifications, and RFP process information
              </p>
            </Link>

            <a href="/capability-statement.pdf" download className="card hover:border-primary group">
              <div className="text-3xl mb-3">📄</div>
              <h3 className="font-bold mb-2 group-hover:text-primary transition-colors">
                Capability Statement
              </h3>
              <p className="text-sm text-gray-600">
                Download our one-page capability statement (PDF)
              </p>
            </a>

            <Link href="/contact" className="card hover:border-primary group">
              <div className="text-3xl mb-3">💬</div>
              <h3 className="font-bold mb-2 group-hover:text-primary transition-colors">
                Contact Support
              </h3>
              <p className="text-sm text-gray-600">
                Get in touch with our team for technical assistance
              </p>
            </Link>
          </div>
        </div>
      </section>

      {/* How to Buy */}
      <section className="section">
        <div className="container">
          <div className="max-w-4xl mx-auto">
            <h2 className="text-center mb-12">How to Buy From Us</h2>
            
            <div className="space-y-8">
              <div className="card">
                <div className="flex items-start">
                  <div className="text-3xl mr-6 flex-shrink-0">1️⃣</div>
                  <div>
                    <h3 className="font-bold text-xl mb-2">Request a Quote</h3>
                    <p className="text-gray-600 mb-3">
                      Contact us with your requirements via phone, email, or our contact form. 
                      Include quantities, specifications, and any special requirements.
                    </p>
                    <Link href="/contact" className="text-primary hover:underline font-semibold">
                      Submit a quote request →
                    </Link>
                  </div>
                </div>
              </div>

              <div className="card">
                <div className="flex items-start">
                  <div className="text-3xl mr-6 flex-shrink-0">2️⃣</div>
                  <div>
                    <h3 className="font-bold text-xl mb-2">Review Proposal</h3>
                    <p className="text-gray-600 mb-3">
                      We'll provide a detailed quote with specifications, pricing, and lead times. 
                      Government buyers will receive compliance documentation as needed.
                    </p>
                    <p className="text-sm text-gray-500">
                      Response time: 24 hours for standard quotes, 4 hours for RFPs
                    </p>
                  </div>
                </div>
              </div>

              <div className="card">
                <div className="flex items-start">
                  <div className="text-3xl mr-6 flex-shrink-0">3️⃣</div>
                  <div>
                    <h3 className="font-bold text-xl mb-2">Place Order</h3>
                    <p className="text-gray-600 mb-3">
                      Submit a purchase order or use your organization's procurement process. 
                      We accept various payment methods including purchase orders, credit cards, and wire transfers.
                    </p>
                  </div>
                </div>
              </div>

              <div className="card">
                <div className="flex items-start">
                  <div className="text-3xl mr-6 flex-shrink-0">4️⃣</div>
                  <div>
                    <h3 className="font-bold text-xl mb-2">Configuration & Testing</h3>
                    <p className="text-gray-600 mb-3">
                      We configure, test, and prepare your systems according to specifications. 
                      Custom imaging, asset tagging, and documentation are included as requested.
                    </p>
                  </div>
                </div>
              </div>

              <div className="card">
                <div className="flex items-start">
                  <div className="text-3xl mr-6 flex-shrink-0">5️⃣</div>
                  <div>
                    <h3 className="font-bold text-xl mb-2">Delivery & Support</h3>
                    <p className="text-gray-600 mb-3">
                      Equipment is shipped with tracking or delivered on-site as arranged. 
                      Post-delivery support and warranty facilitation are included.
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* FAQs */}
      <section className="section bg-gray-50">
        <div className="container">
          <div className="max-w-4xl mx-auto">
            <h2 className="text-center mb-12">Frequently Asked Questions</h2>

            {categories.map((category) => {
              const categoryFaqs = faqs.filter((faq) => faq.category === category);
              if (categoryFaqs.length === 0) return null;

              return (
                <div key={category} className="mb-12">
                  <h3 className="text-xl font-bold mb-6 text-primary">{category}</h3>
                  <div className="space-y-6">
                    {categoryFaqs.map((faq, index) => (
                      <div key={index} className="card">
                        <h4 className="font-bold text-lg mb-3">{faq.question}</h4>
                        <p className="text-gray-700">{faq.answer}</p>
                      </div>
                    ))}
                  </div>
                </div>
              );
            })}
          </div>
        </div>
      </section>

      {/* Contact CTA */}
      <section className="section bg-primary text-white">
        <div className="container text-center">
          <h2 className="mb-4">Still Have Questions?</h2>
          <p className="text-xl mb-8 text-blue-100 max-w-2xl mx-auto">
            Our team is here to help. Contact us for personalized assistance with your IT requirements.
          </p>
          <div className="flex flex-col sm:flex-row gap-4 justify-center">
            <Link href="/contact" className="btn btn-large" style={{ backgroundColor: 'white', color: 'var(--color-primary)' }}>
              Contact Us
            </Link>
            <a href="tel:8004741388" className="btn btn-large btn-secondary" style={{ borderColor: 'white', color: 'white' }}>
              📞 (800) 474-1388
            </a>
          </div>
        </div>
      </section>
    </>
  );
}
