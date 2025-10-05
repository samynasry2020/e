import { Phone, Mail, MapPin, Clock } from "lucide-react";
import { ContactForm } from "@/components/forms/ContactForm";
import { RFPForm } from "@/components/forms/RFPForm";

export const metadata = {
  title: "Contact Us - Get in Touch",
  description: "Contact Pubuild for IT hardware solutions and government contracting services. Call 800-474-1388 or submit our contact form.",
};

export default function ContactPage() {
  return (
    <div className="min-h-screen bg-gray-50">
      {/* Hero Section */}
      <section className="bg-white py-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center">
            <h1 className="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
              Contact Us
            </h1>
            <p className="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
              Ready to discuss your IT hardware needs or government contracting requirements?
              Get in touch with our expert team today.
            </p>
          </div>
        </div>
      </section>

      {/* Contact Information & Forms */}
      <section className="py-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-1 lg:grid-cols-3 gap-12">
            {/* Contact Information */}
            <div className="lg:col-span-1">
              <div className="bg-white rounded-lg shadow-sm p-8">
                <h2 className="text-2xl font-bold text-gray-900 mb-8">
                  Get in Touch
                </h2>

                <div className="space-y-6">
                  <div className="flex items-start space-x-4">
                    <div className="bg-primary/10 rounded-lg p-3">
                      <Phone className="h-6 w-6 text-primary" />
                    </div>
                    <div>
                      <h3 className="font-semibold text-gray-900">Phone</h3>
                      <p className="text-gray-600">800-474-1388</p>
                      <p className="text-sm text-gray-500 mt-1">
                        Mon-Fri: 8:00 AM - 6:00 PM PST
                      </p>
                    </div>
                  </div>

                  <div className="flex items-start space-x-4">
                    <div className="bg-primary/10 rounded-lg p-3">
                      <Mail className="h-6 w-6 text-primary" />
                    </div>
                    <div>
                      <h3 className="font-semibold text-gray-900">Email</h3>
                      <p className="text-gray-600">sales@pubuild.com</p>
                      <p className="text-sm text-gray-500 mt-1">
                        We respond within 24 hours
                      </p>
                    </div>
                  </div>

                  <div className="flex items-start space-x-4">
                    <div className="bg-primary/10 rounded-lg p-3">
                      <MapPin className="h-6 w-6 text-primary" />
                    </div>
                    <div>
                      <h3 className="font-semibold text-gray-900">Office</h3>
                      <p className="text-gray-600">
                        Physical address available<br />
                        upon request for security
                      </p>
                    </div>
                  </div>

                  <div className="flex items-start space-x-4">
                    <div className="bg-primary/10 rounded-lg p-3">
                      <Clock className="h-6 w-6 text-primary" />
                    </div>
                    <div>
                      <h3 className="font-semibold text-gray-900">Response Time</h3>
                      <p className="text-gray-600">
                        General inquiries: 24 hours<br />
                        RFP reviews: 48 hours<br />
                        Urgent matters: 4 hours
                      </p>
                    </div>
                  </div>
                </div>

                <div className="mt-8 p-4 bg-blue-50 rounded-lg">
                  <h4 className="font-semibold text-blue-900 mb-2">
                    Government Contracting?
                  </h4>
                  <p className="text-sm text-blue-700">
                    For federal procurement inquiries, please use our RFP form
                    or contact us directly for expedited service.
                  </p>
                </div>
              </div>
            </div>

            {/* Contact Forms */}
            <div className="lg:col-span-2 space-y-8">
              {/* General Contact Form */}
              <div className="bg-white rounded-lg shadow-sm p-8">
                <h2 className="text-2xl font-bold text-gray-900 mb-2">
                  Send us a Message
                </h2>
                <p className="text-gray-600 mb-8">
                  Have questions about our products or services? We're here to help.
                </p>
                <ContactForm />
              </div>

              {/* RFP Form */}
              <div className="bg-white rounded-lg shadow-sm p-8">
                <h2 className="text-2xl font-bold text-gray-900 mb-2">
                  RFP & Government Projects
                </h2>
                <p className="text-gray-600 mb-8">
                  Submit your RFP for review or discuss government contracting opportunities.
                </p>
                <RFPForm />
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Additional Information */}
      <section className="py-20 bg-gray-100">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-16">
            <h2 className="text-3xl font-bold text-gray-900 mb-4">
              Why Work With Pubuild?
            </h2>
            <p className="text-xl text-gray-600 max-w-2xl mx-auto">
              We're more than just a vendor—we're your technology partner.
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div className="bg-white rounded-lg p-8 text-center">
              <div className="bg-blue-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                <div className="w-8 h-8 bg-blue-600 rounded"></div>
              </div>
              <h3 className="text-xl font-semibold text-gray-900 mb-2">
                Technical Expertise
              </h3>
              <p className="text-gray-600">
                Deep knowledge of enterprise IT hardware and government procurement processes.
              </p>
            </div>

            <div className="bg-white rounded-lg p-8 text-center">
              <div className="bg-green-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                <div className="w-8 h-8 bg-green-600 rounded"></div>
              </div>
              <h3 className="text-xl font-semibold text-gray-900 mb-2">
                Fast Response
              </h3>
              <p className="text-gray-600">
                Quick turnaround on quotes, technical questions, and RFP responses.
              </p>
            </div>

            <div className="bg-white rounded-lg p-8 text-center">
              <div className="bg-purple-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                <div className="w-8 h-8 bg-purple-600 rounded"></div>
              </div>
              <h3 className="text-xl font-semibold text-gray-900 mb-2">
                Full Support
              </h3>
              <p className="text-gray-600">
                From initial consultation through deployment and ongoing support.
              </p>
            </div>
          </div>
        </div>
      </section>
    </div>
  );
}