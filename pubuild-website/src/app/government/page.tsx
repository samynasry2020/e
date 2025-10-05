import Layout from '@/components/layout/layout'
import { FileText, Download, Phone, Mail, CheckCircle, AlertTriangle } from 'lucide-react'
import Link from 'next/link'
import { Button } from '@/components/ui/button'

const naicsCodes = [
  { code: '334111', description: 'Electronic Computer Manufacturing' },
  { code: '423430', description: 'Computer and Computer Peripheral Equipment Merchant Wholesalers' },
  { code: '541512', description: 'Computer Systems Design Services' },
  { code: '541511', description: 'Custom Computer Programming Services' },
  { code: '541519', description: 'Other Computer Related Services' }
]

const capabilities = [
  'Federal Government Contracting',
  'State & Local Government Procurement',
  'GSA Schedule Compliance',
  'Export Control Compliance',
  'Security Clearance Requirements',
  'Custom Hardware Solutions',
  'RFP Response & Proposal Support',
  'Contract Management & Administration'
]

const services = [
  {
    title: 'Hardware Procurement',
    description: 'Complete IT hardware solutions for government agencies',
    features: ['Servers & Workstations', 'Networking Equipment', 'Storage Solutions', 'Security Hardware']
  },
  {
    title: 'Custom Solutions',
    description: 'Tailored infrastructure solutions for specific requirements',
    features: ['Custom Configurations', 'Compliance Documentation', 'Security Hardening', 'Testing & Validation']
  },
  {
    title: 'Support Services',
    description: 'Ongoing support and maintenance for government contracts',
    features: ['24/7 Technical Support', 'Warranty Management', 'Lifecycle Support', 'Asset Management']
  }
]

export default function GovernmentPage() {
  return (
    <Layout>
      <div className="bg-white">
        {/* Hero Section */}
        <div className="bg-gradient-to-r from-primary to-secondary text-white py-20">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="text-center">
              <h1 className="text-4xl md:text-5xl font-bold mb-4">
                Government Contracting Services
              </h1>
              <p className="text-xl text-blue-100 max-w-3xl mx-auto">
                Trusted IT hardware supplier for government agencies. We understand 
                federal procurement requirements and deliver compliant, secure solutions 
                that meet the highest standards.
              </p>
            </div>
          </div>
        </div>

        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
          {/* Important Notice */}
          <div className="mb-16 bg-yellow-50 border border-yellow-200 rounded-lg p-6">
            <div className="flex items-start">
              <AlertTriangle className="h-6 w-6 text-yellow-600 mr-3 mt-0.5" />
              <div>
                <h3 className="text-lg font-semibold text-yellow-800 mb-2">Important Notice</h3>
                <p className="text-yellow-700 mb-2">
                  <strong>We are an independent supplier. No government endorsement implied.</strong>
                </p>
                <p className="text-yellow-700 text-sm">
                  Products may be subject to U.S. export controls; purchaser is responsible for compliance.
                </p>
              </div>
            </div>
          </div>

          {/* NAICS Codes */}
          <div className="mb-16">
            <h2 className="text-3xl font-bold text-gray-900 mb-8 text-center">
              NAICS Codes & Classifications
            </h2>
            <div className="bg-gray-50 rounded-lg p-8">
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {naicsCodes.map((naics, index) => (
                  <div key={index} className="bg-white rounded-lg p-4 border border-gray-200">
                    <div className="text-2xl font-bold text-primary mb-2">{naics.code}</div>
                    <div className="text-gray-700 text-sm">{naics.description}</div>
                  </div>
                ))}
              </div>
              <div className="mt-6 p-4 bg-blue-50 rounded-lg border border-blue-200">
                <p className="text-sm text-blue-800">
                  <strong>Note:</strong> Additional NAICS codes and certifications may be available. 
                  Contact us for current status and specific requirements.
                </p>
              </div>
            </div>
          </div>

          {/* Capabilities */}
          <div className="mb-16">
            <h2 className="text-3xl font-bold text-gray-900 mb-8 text-center">
              Our Government Capabilities
            </h2>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
              {capabilities.map((capability, index) => (
                <div key={index} className="bg-white rounded-lg p-6 shadow-md border border-gray-200">
                  <div className="flex items-center mb-3">
                    <CheckCircle className="h-6 w-6 text-green-500 mr-3" />
                    <span className="font-semibold text-gray-900">{capability}</span>
                  </div>
                </div>
              ))}
            </div>
          </div>

          {/* Services */}
          <div className="mb-16">
            <h2 className="text-3xl font-bold text-gray-900 mb-8 text-center">
              Government Services
            </h2>
            <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
              {services.map((service, index) => (
                <div key={index} className="bg-gray-50 rounded-lg p-6">
                  <h3 className="text-xl font-semibold text-gray-900 mb-3">
                    {service.title}
                  </h3>
                  <p className="text-gray-600 mb-4">
                    {service.description}
                  </p>
                  <ul className="space-y-2">
                    {service.features.map((feature, idx) => (
                      <li key={idx} className="flex items-start">
                        <div className="w-2 h-2 bg-primary rounded-full mt-2 mr-3 flex-shrink-0"></div>
                        <span className="text-gray-700 text-sm">{feature}</span>
                      </li>
                    ))}
                  </ul>
                </div>
              ))}
            </div>
          </div>

          {/* RFP Support */}
          <div className="mb-16">
            <h2 className="text-3xl font-bold text-gray-900 mb-8 text-center">
              RFP Support & Proposal Services
            </h2>
            <div className="bg-primary text-white rounded-2xl p-8 lg:p-12">
              <div className="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
                <div>
                  <h3 className="text-2xl font-bold mb-4">
                    Need Help with Government Proposals?
                  </h3>
                  <p className="text-xl text-blue-100 mb-6">
                    Our team has extensive experience with government RFP responses 
                    and can help you navigate the procurement process.
                  </p>
                  <ul className="space-y-3 mb-8">
                    <li className="flex items-center">
                      <CheckCircle className="h-5 w-5 text-blue-200 mr-3" />
                      <span>RFP analysis and response</span>
                    </li>
                    <li className="flex items-center">
                      <CheckCircle className="h-5 w-5 text-blue-200 mr-3" />
                      <span>Technical specifications</span>
                    </li>
                    <li className="flex items-center">
                      <CheckCircle className="h-5 w-5 text-blue-200 mr-3" />
                      <span>Compliance documentation</span>
                    </li>
                    <li className="flex items-center">
                      <CheckCircle className="h-5 w-5 text-blue-200 mr-3" />
                      <span>Pricing and terms</span>
                    </li>
                  </ul>
                  <Button asChild size="lg" className="bg-white text-primary hover:bg-gray-100">
                    <Link href="/contact">
                      Submit RFP Request
                    </Link>
                  </Button>
                </div>
                <div className="bg-white/10 backdrop-blur-sm rounded-xl p-6">
                  <h4 className="text-lg font-semibold mb-4">RFP Information Needed</h4>
                  <ul className="space-y-2 text-sm">
                    <li>• Agency name and contract number</li>
                    <li>• Proposal deadline</li>
                    <li>• Technical requirements</li>
                    <li>• Budget range</li>
                    <li>• Special compliance needs</li>
                    <li>• Contact information</li>
                  </ul>
                </div>
              </div>
            </div>
          </div>

          {/* Capability Statement */}
          <div className="mb-16">
            <h2 className="text-3xl font-bold text-gray-900 mb-8 text-center">
              Capability Statement
            </h2>
            <div className="bg-gray-50 rounded-lg p-8 text-center">
              <div className="max-w-2xl mx-auto">
                <Download className="h-16 w-16 text-primary mx-auto mb-4" />
                <h3 className="text-2xl font-bold text-gray-900 mb-4">
                  Download Our Capability Statement
                </h3>
                <p className="text-gray-600 mb-6">
                  Get our one-page capability statement with company information, 
                  NAICS codes, and key capabilities for your records.
                </p>
                <Button asChild size="lg" className="bg-primary text-white hover:bg-primary/90">
                  <Link href="/capability-statement.pdf" target="_blank">
                    Download PDF
                  </Link>
                </Button>
              </div>
            </div>
          </div>

          {/* Contact Information */}
          <div className="bg-gray-900 text-white rounded-2xl p-8 lg:p-12">
            <div className="text-center">
              <h2 className="text-3xl font-bold mb-4">
                Contact Our Government Team
              </h2>
              <p className="text-xl text-gray-300 mb-8 max-w-3xl mx-auto">
                Ready to discuss your government IT hardware requirements? 
                Our team is here to help with procurement, compliance, and support.
              </p>
              <div className="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
                <div className="text-center">
                  <Phone className="h-8 w-8 text-blue-400 mx-auto mb-3" />
                  <div className="text-lg font-semibold">Phone</div>
                  <div className="text-gray-300">800-474-1388</div>
                </div>
                <div className="text-center">
                  <Mail className="h-8 w-8 text-blue-400 mx-auto mb-3" />
                  <div className="text-lg font-semibold">Email</div>
                  <div className="text-gray-300">sales@pubuild.com</div>
                </div>
                <div className="text-center">
                  <FileText className="h-8 w-8 text-blue-400 mx-auto mb-3" />
                  <div className="text-lg font-semibold">Response Time</div>
                  <div className="text-gray-300">Within 24 hours</div>
                </div>
              </div>
              <div className="flex flex-col sm:flex-row gap-4 justify-center">
                <Button asChild size="lg" className="bg-blue-600 hover:bg-blue-700">
                  <Link href="/contact">
                    Contact Government Team
                  </Link>
                </Button>
                <Button asChild size="lg" variant="outline" className="border-white text-white hover:bg-white hover:text-gray-900">
                  <Link href="/contact">
                    Submit RFP
                  </Link>
                </Button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Layout>
  )
}