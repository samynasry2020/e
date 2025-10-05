import Link from 'next/link'
import { Button } from '@/components/ui/button'
import { Shield, FileText, Award, Download, ArrowRight } from 'lucide-react'

const certifications = [
  { name: 'NAICS 334111', description: 'Electronic Computer Manufacturing' },
  { name: 'NAICS 423430', description: 'Computer and Computer Peripheral Equipment Merchant Wholesalers' },
  { name: 'NAICS 541512', description: 'Computer Systems Design Services' },
]

const capabilities = [
  'Federal Government Contracting',
  'State & Local Government Procurement',
  'GSA Schedule Compliance',
  'Export Control Compliance',
  'Security Clearance Requirements',
  'Custom Hardware Solutions'
]

export default function GovernmentSection() {
  return (
    <section className="py-20 bg-gray-900 text-white">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="text-center mb-16">
          <h2 className="text-3xl md:text-4xl font-bold mb-4">
            Government Contracting Services
          </h2>
          <p className="text-xl text-gray-300 max-w-3xl mx-auto">
            Trusted IT hardware supplier for government agencies. 
            We understand federal procurement requirements and deliver 
            compliant, secure solutions.
          </p>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center mb-16">
          <div>
            <h3 className="text-2xl font-bold mb-6">Our Government Capabilities</h3>
            <ul className="space-y-4">
              {capabilities.map((capability, index) => (
                <li key={index} className="flex items-start">
                  <div className="w-2 h-2 bg-blue-400 rounded-full mt-2 mr-3 flex-shrink-0"></div>
                  <span className="text-gray-300">{capability}</span>
                </li>
              ))}
            </ul>
            
            <div className="mt-8 p-6 bg-blue-900/30 rounded-lg border border-blue-800">
              <p className="text-sm text-blue-200 mb-2">
                <strong>Important:</strong> We are an independent supplier. No government endorsement implied.
              </p>
              <p className="text-sm text-blue-200">
                Products may be subject to U.S. export controls; purchaser is responsible for compliance.
              </p>
            </div>
          </div>

          <div className="bg-white/5 backdrop-blur-sm rounded-xl p-8">
            <h4 className="text-xl font-semibold mb-6 flex items-center">
              <Shield className="h-6 w-6 mr-2 text-blue-400" />
              NAICS Codes
            </h4>
            <div className="space-y-4">
              {certifications.map((cert, index) => (
                <div key={index} className="flex justify-between items-center py-2 border-b border-gray-700 last:border-b-0">
                  <div>
                    <div className="font-medium">{cert.name}</div>
                    <div className="text-sm text-gray-400">{cert.description}</div>
                  </div>
                </div>
              ))}
            </div>
            
            <div className="mt-6 p-4 bg-yellow-900/20 rounded-lg border border-yellow-800">
              <p className="text-sm text-yellow-200">
                <strong>Note:</strong> Certifications and set-aside statuses will be updated 
                as they are obtained. Contact us for current status.
              </p>
            </div>
          </div>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-3 gap-8 mb-16">
          <div className="text-center">
            <div className="bg-blue-600 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
              <FileText className="h-8 w-8" />
            </div>
            <h4 className="text-xl font-semibold mb-2">RFP Support</h4>
            <p className="text-gray-300 text-sm">
              Complete proposal support for government IT hardware requirements
            </p>
          </div>
          
          <div className="text-center">
            <div className="bg-blue-600 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
              <Award className="h-8 w-8" />
            </div>
            <h4 className="text-xl font-semibold mb-2">Compliance Ready</h4>
            <p className="text-gray-300 text-sm">
              All products meet federal security and compliance standards
            </p>
          </div>
          
          <div className="text-center">
            <div className="bg-blue-600 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
              <Download className="h-8 w-8" />
            </div>
            <h4 className="text-xl font-semibold mb-2">Capability Statement</h4>
            <p className="text-gray-300 text-sm">
              Download our one-page capability statement for your records
            </p>
          </div>
        </div>

        <div className="text-center">
          <div className="flex flex-col sm:flex-row gap-4 justify-center">
            <Button asChild size="lg" className="bg-blue-600 hover:bg-blue-700">
              <Link href="/government">
                Learn More About Government Services
                <ArrowRight className="ml-2 h-5 w-5" />
              </Link>
            </Button>
            <Button asChild size="lg" variant="outline" className="border-white text-white hover:bg-white hover:text-gray-900">
              <Link href="/contact">
                Submit RFP Request
              </Link>
            </Button>
          </div>
        </div>
      </div>
    </section>
  )
}