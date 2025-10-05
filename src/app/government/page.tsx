import Link from "next/link";
import { ArrowRight, Download, Shield, Users, CheckCircle, FileText } from "lucide-react";
import { governmentInfo } from "@/data/government";
import { Button } from "@/components/ui/Button";

export const metadata = {
  title: "Government Contracting - Federal IT Solutions",
  description: "Pubuild is a trusted government contractor providing IT hardware solutions to federal agencies. NAICS codes: 334111, 423430, 541512.",
};

export default function GovernmentPage() {
  const { naicsCodes, certifications, capabilityStatement, pscCodes } = governmentInfo;

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Hero Section */}
      <section className="bg-gradient-to-br from-blue-900 to-blue-800 py-20 text-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center">
            <div className="flex justify-center mb-6">
              <div className="bg-white/10 rounded-full p-4">
                <Shield className="h-12 w-12 text-white" />
              </div>
            </div>
            <h1 className="text-4xl md:text-5xl font-bold mb-6">
              Government Contracting
            </h1>
            <p className="text-xl mb-8 max-w-3xl mx-auto text-blue-100">
              Trusted partner for federal agencies, providing IT hardware solutions and
              comprehensive technology services with full compliance and expertise.
            </p>
            <div className="flex flex-col sm:flex-row gap-4 justify-center">
              <Button size="lg" variant="secondary" asChild>
                <Link href="#capability-statement">
                  Download Capability Statement <Download className="ml-2 h-5 w-5" />
                </Link>
              </Button>
              <Button size="lg" variant="outline" asChild>
                <Link href="/contact">
                  Start a Project <ArrowRight className="ml-2 h-5 w-5" />
                </Link>
              </Button>
            </div>
          </div>
        </div>
      </section>

      {/* NAICS Codes & Certifications */}
      <section className="py-20 bg-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-16">
            {/* NAICS Codes */}
            <div>
              <h2 className="text-3xl font-bold text-gray-900 mb-8">
                NAICS Codes
              </h2>
              <p className="text-gray-600 mb-6">
                We are registered and qualified to provide services under the following
                North American Industry Classification System codes:
              </p>
              <div className="space-y-4">
                {naicsCodes.map((naics) => (
                  <div key={naics.code} className="bg-gray-50 rounded-lg p-6">
                    <div className="flex items-start justify-between mb-2">
                      <div className="font-mono text-lg font-bold text-primary">
                        {naics.code}
                      </div>
                      <span className={`px-2 py-1 rounded-full text-xs font-medium ${
                        naics.certification === 'Active'
                          ? 'bg-green-100 text-green-800'
                          : 'bg-yellow-100 text-yellow-800'
                      }`}>
                        {naics.certification}
                      </span>
                    </div>
                    <p className="text-gray-700">{naics.description}</p>
                  </div>
                ))}
              </div>
            </div>

            {/* Certifications */}
            <div>
              <h2 className="text-3xl font-bold text-gray-900 mb-8">
                Certifications & Compliance
              </h2>
              <p className="text-gray-600 mb-6">
                We maintain active certifications and comply with federal requirements:
              </p>
              <div className="space-y-4">
                {certifications.map((cert, index) => (
                  <div key={index} className="bg-gray-50 rounded-lg p-6">
                    <div className="flex items-start justify-between mb-2">
                      <h3 className="font-semibold text-gray-900">{cert.name}</h3>
                      <span className={`px-2 py-1 rounded-full text-xs font-medium ${
                        cert.status === 'active'
                          ? 'bg-green-100 text-green-800'
                          : 'bg-yellow-100 text-yellow-800'
                      }`}>
                        {cert.status === 'active' ? 'Active' : 'Pending'}
                      </span>
                    </div>
                    {cert.certificationId && (
                      <p className="text-sm text-gray-600 mb-1">
                        ID: {cert.certificationId}
                      </p>
                    )}
                    {cert.expiryDate && (
                      <p className="text-sm text-gray-600">
                        Expires: {new Date(cert.expiryDate).toLocaleDateString()}
                      </p>
                    )}
                  </div>
                ))}
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* PSC Codes (if available) */}
      {pscCodes && pscCodes.length > 0 && (
        <section className="py-20 bg-gray-50">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 className="text-3xl font-bold text-gray-900 mb-8 text-center">
              Product Service Codes (PSC)
            </h2>
            <p className="text-gray-600 mb-8 text-center max-w-2xl mx-auto">
              We provide services under the following Product Service Code families:
            </p>
            <div className="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-4">
              {pscCodes.map((code) => (
                <div key={code} className="bg-white rounded-lg p-4 text-center">
                  <div className="font-mono text-lg font-bold text-primary">{code}</div>
                </div>
              ))}
            </div>
          </div>
        </section>
      )}

      {/* Capability Statement */}
      <section id="capability-statement" className="py-20 bg-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-16">
            <h2 className="text-3xl font-bold text-gray-900 mb-4">
              Capability Statement
            </h2>
            <p className="text-xl text-gray-600 max-w-2xl mx-auto">
              Download our comprehensive capability statement for government procurement use.
            </p>
          </div>

          <div className="bg-gray-50 rounded-lg p-8 mb-8">
            <h3 className="text-2xl font-bold text-gray-900 mb-6">
              {capabilityStatement.title}
            </h3>

            <div className="prose prose-lg max-w-none text-gray-700 mb-8">
              {capabilityStatement.content.split('\n\n').map((paragraph, index) => (
                <p key={index} className="mb-4">
                  {paragraph}
                </p>
              ))}
            </div>

            <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
              <div>
                <h4 className="text-lg font-semibold text-gray-900 mb-4">
                  Our Expertise
                </h4>
                <ul className="space-y-2">
                  {capabilityStatement.content.match(/• ([^\n]+)/g)?.map((item, index) => (
                    <li key={index} className="flex items-start">
                      <CheckCircle className="h-5 w-5 text-green-600 mt-0.5 mr-2 flex-shrink-0" />
                      <span className="text-gray-700">{item.replace('• ', '')}</span>
                    </li>
                  ))}
                </ul>
              </div>

              <div>
                <h4 className="text-lg font-semibold text-gray-900 mb-4">
                  Key Differentiators
                </h4>
                <ul className="space-y-2">
                  {capabilityStatement.differentiators.map((diff, index) => (
                    <li key={index} className="flex items-start">
                      <CheckCircle className="h-5 w-5 text-green-600 mt-0.5 mr-2 flex-shrink-0" />
                      <span className="text-gray-700">{diff}</span>
                    </li>
                  ))}
                </ul>
              </div>
            </div>
          </div>

          <div className="text-center">
            <Button size="lg" asChild>
              <Link href="/capability-statement.pdf" download>
                <Download className="mr-2 h-5 w-5" />
                Download PDF Version
              </Link>
            </Button>
          </div>
        </div>
      </section>

      {/* RFP Support */}
      <section className="py-20 bg-primary">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center text-white">
            <div className="flex justify-center mb-6">
              <div className="bg-white/10 rounded-full p-4">
                <FileText className="h-12 w-12 text-white" />
              </div>
            </div>
            <h2 className="text-3xl font-bold mb-6">
              RFP & Bid Support
            </h2>
            <p className="text-xl mb-8 max-w-3xl mx-auto text-blue-100">
              Need help with your next government procurement? We provide comprehensive
              RFP support and technical expertise to help you succeed.
            </p>

            <div className="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
              <div className="bg-white/10 rounded-lg p-6">
                <h3 className="text-lg font-semibold mb-2">Technical Proposals</h3>
                <p className="text-blue-100">
                  Detailed technical responses tailored to your specific requirements
                </p>
              </div>
              <div className="bg-white/10 rounded-lg p-6">
                <h3 className="text-lg font-semibold mb-2">Pricing Support</h3>
                <p className="text-blue-100">
                  Competitive pricing with detailed breakdowns and justifications
                </p>
              </div>
              <div className="bg-white/10 rounded-lg p-6">
                <h3 className="text-lg font-semibold mb-2">Compliance Review</h3>
                <p className="text-blue-100">
                  Ensuring all submissions meet federal compliance requirements
                </p>
              </div>
            </div>

            <Button size="lg" variant="secondary" asChild>
              <Link href="/contact?type=rfp">
                Submit RFP for Review <ArrowRight className="ml-2 h-5 w-5" />
              </Link>
            </Button>
          </div>
        </div>
      </section>

      {/* Important Disclaimers */}
      <section className="py-16 bg-gray-100">
        <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded-r-lg">
            <h3 className="text-lg font-semibold text-yellow-800 mb-2">
              Important Disclaimers
            </h3>
            <div className="text-yellow-700 space-y-2">
              <p>
                <strong>We are an independent supplier.</strong> No government endorsement implied.
                We are not affiliated with any government agency or OEM manufacturer.
              </p>
              <p>
                <strong>Certification Status:</strong> All certifications listed are current as of the
                date shown. Status may change; please verify current status with the issuing authority.
              </p>
              <p>
                <strong>Export Compliance:</strong> Products may be subject to U.S. export controls.
                Purchaser is responsible for compliance with all applicable export regulations.
              </p>
            </div>
          </div>
        </div>
      </section>
    </div>
  );
}