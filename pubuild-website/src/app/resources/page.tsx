import Layout from '@/components/layout/layout'
import { FileText, Download, BookOpen, HelpCircle } from 'lucide-react'
import Link from 'next/link'
import { Button } from '@/components/ui/button'

const resources = [
  {
    title: 'Product Guides',
    description: 'Comprehensive guides for our hardware products and solutions',
    icon: BookOpen,
    items: [
      { name: 'Server Configuration Guide', type: 'PDF', size: '2.1 MB' },
      { name: 'Workstation Setup Manual', type: 'PDF', size: '1.8 MB' },
      { name: 'Networking Best Practices', type: 'PDF', size: '1.5 MB' },
      { name: 'Storage Solutions Overview', type: 'PDF', size: '1.2 MB' }
    ]
  },
  {
    title: 'Government Resources',
    description: 'Resources for government contracting and compliance',
    icon: FileText,
    items: [
      { name: 'Capability Statement', type: 'PDF', size: '0.8 MB' },
      { name: 'NAICS Code Reference', type: 'PDF', size: '0.5 MB' },
      { name: 'RFP Response Template', type: 'DOCX', size: '0.3 MB' },
      { name: 'Compliance Checklist', type: 'PDF', size: '0.6 MB' }
    ]
  },
  {
    title: 'Technical Documentation',
    description: 'Technical specifications and installation guides',
    icon: HelpCircle,
    items: [
      { name: 'Hardware Specifications', type: 'PDF', size: '3.2 MB' },
      { name: 'Installation Procedures', type: 'PDF', size: '2.5 MB' },
      { name: 'Troubleshooting Guide', type: 'PDF', size: '1.9 MB' },
      { name: 'Warranty Information', type: 'PDF', size: '0.4 MB' }
    ]
  }
]

const faqs = [
  {
    question: 'What NAICS codes does Pubuild use?',
    answer: 'We primarily use NAICS codes 334111 (Electronic Computer Manufacturing), 423430 (Computer and Computer Peripheral Equipment Merchant Wholesalers), and 541512 (Computer Systems Design Services).'
  },
  {
    question: 'Do you provide government contracting support?',
    answer: 'Yes, we offer comprehensive government contracting services including RFP response, compliance documentation, and procurement support for federal, state, and local agencies.'
  },
  {
    question: 'What types of hardware do you supply?',
    answer: 'We supply enterprise-grade servers, workstations, GPUs, storage solutions, networking equipment, monitors, and accessories. All products meet government security and compliance standards.'
  },
  {
    question: 'Do you offer custom configurations?',
    answer: 'Yes, we can provide custom hardware configurations tailored to your specific requirements, including security hardening and compliance modifications.'
  },
  {
    question: 'What is your response time for quotes?',
    answer: 'We typically respond to quote requests within 24 hours. For urgent government contracting inquiries, we can provide faster response times.'
  },
  {
    question: 'Do you provide installation and support services?',
    answer: 'Yes, we offer installation, configuration, and ongoing support services for all our hardware solutions. Our team includes certified technicians and engineers.'
  }
]

export default function ResourcesPage() {
  return (
    <Layout>
      <div className="bg-white">
        {/* Hero Section */}
        <div className="bg-gradient-to-r from-primary to-secondary text-white py-20">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="text-center">
              <h1 className="text-4xl md:text-5xl font-bold mb-4">
                Resources & Documentation
              </h1>
              <p className="text-xl text-blue-100 max-w-3xl mx-auto">
                Access our comprehensive library of product guides, technical documentation, 
                and government contracting resources to help you make informed decisions.
              </p>
            </div>
          </div>
        </div>

        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
          {/* Resource Categories */}
          <div className="mb-16">
            <h2 className="text-3xl font-bold text-gray-900 mb-8 text-center">
              Resource Library
            </h2>
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
              {resources.map((resource, index) => {
                const Icon = resource.icon
                return (
                  <div key={index} className="bg-gray-50 rounded-xl p-6">
                    <div className="flex items-center mb-4">
                      <div className="p-3 bg-primary text-white rounded-lg">
                        <Icon className="h-6 w-6" />
                      </div>
                      <h3 className="text-xl font-semibold text-gray-900 ml-3">
                        {resource.title}
                      </h3>
                    </div>
                    <p className="text-gray-600 mb-4">
                      {resource.description}
                    </p>
                    <div className="space-y-2">
                      {resource.items.map((item, idx) => (
                        <div key={idx} className="flex items-center justify-between p-2 bg-white rounded border">
                          <div className="flex items-center">
                            <FileText className="h-4 w-4 text-gray-400 mr-2" />
                            <span className="text-sm text-gray-700">{item.name}</span>
                          </div>
                          <div className="flex items-center space-x-2">
                            <span className="text-xs text-gray-500">{item.type}</span>
                            <span className="text-xs text-gray-400">{item.size}</span>
                            <Button size="sm" variant="ghost">
                              <Download className="h-4 w-4" />
                            </Button>
                          </div>
                        </div>
                      ))}
                    </div>
                  </div>
                )
              })}
            </div>
          </div>

          {/* How to Buy */}
          <div className="mb-16">
            <h2 className="text-3xl font-bold text-gray-900 mb-8 text-center">
              How to Buy with Us
            </h2>
            <div className="bg-gray-50 rounded-xl p-8">
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <div className="text-center">
                  <div className="bg-primary text-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4 text-2xl font-bold">
                    1
                  </div>
                  <h3 className="text-lg font-semibold text-gray-900 mb-2">Contact Us</h3>
                  <p className="text-gray-600 text-sm">
                    Reach out with your requirements via phone, email, or our contact form
                  </p>
                </div>
                
                <div className="text-center">
                  <div className="bg-primary text-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4 text-2xl font-bold">
                    2
                  </div>
                  <h3 className="text-lg font-semibold text-gray-900 mb-2">Get Quote</h3>
                  <p className="text-gray-600 text-sm">
                    We'll provide a detailed quote with specifications and pricing
                  </p>
                </div>
                
                <div className="text-center">
                  <div className="bg-primary text-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4 text-2xl font-bold">
                    3
                  </div>
                  <h3 className="text-lg font-semibold text-gray-900 mb-2">Place Order</h3>
                  <p className="text-gray-600 text-sm">
                    Review and approve the quote, then place your order
                  </p>
                </div>
                
                <div className="text-center">
                  <div className="bg-primary text-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4 text-2xl font-bold">
                    4
                  </div>
                  <h3 className="text-lg font-semibold text-gray-900 mb-2">Delivery & Support</h3>
                  <p className="text-gray-600 text-sm">
                    We deliver, install, and provide ongoing support for your solution
                  </p>
                </div>
              </div>
            </div>
          </div>

          {/* FAQ Section */}
          <div className="mb-16">
            <h2 className="text-3xl font-bold text-gray-900 mb-8 text-center">
              Frequently Asked Questions
            </h2>
            <div className="max-w-3xl mx-auto">
              <div className="space-y-6">
                {faqs.map((faq, index) => (
                  <div key={index} className="bg-white border border-gray-200 rounded-lg p-6">
                    <h3 className="text-lg font-semibold text-gray-900 mb-3">
                      {faq.question}
                    </h3>
                    <p className="text-gray-700">
                      {faq.answer}
                    </p>
                  </div>
                ))}
              </div>
            </div>
          </div>

          {/* Contact CTA */}
          <div className="bg-primary text-white rounded-2xl p-8 lg:p-12 text-center">
            <h2 className="text-3xl font-bold mb-4">
              Need More Information?
            </h2>
            <p className="text-xl text-blue-100 mb-8 max-w-3xl mx-auto">
                Can't find what you're looking for? Our team is here to help with
              any questions about our products, services, or government contracting.
            </p>
            <div className="flex flex-col sm:flex-row gap-4 justify-center">
              <Button asChild size="lg" className="bg-white text-primary hover:bg-gray-100">
                <Link href="/contact">
                  Contact Us
                </Link>
              </Button>
              <Button asChild size="lg" variant="outline" className="border-white text-white hover:bg-white hover:text-primary">
                <Link href="/government">
                  Government Services
                </Link>
              </Button>
            </div>
          </div>
        </div>
      </div>
    </Layout>
  )
}