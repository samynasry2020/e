import Layout from '@/components/layout/layout'
import { Shield, Users, Award, Target, CheckCircle } from 'lucide-react'

const values = [
  {
    icon: Shield,
    title: 'Security First',
    description: 'We prioritize security and compliance in everything we do, ensuring our solutions meet the highest government standards.'
  },
  {
    icon: Users,
    title: 'Customer Focus',
    description: 'Our customers success is our success. We work closely with each client to understand and meet their unique requirements.'
  },
  {
    icon: Award,
    title: 'Quality Excellence',
    description: 'We deliver enterprise-grade hardware and solutions that meet the most demanding performance and reliability requirements.'
  },
  {
    icon: Target,
    title: 'Mission Critical',
    description: 'We understand the importance of mission-critical systems and deliver solutions that organizations can depend on.'
  }
]

const team = [
  {
    name: 'Technical Team',
    description: 'Certified engineers and technicians with expertise in government IT requirements and compliance standards.'
  },
  {
    name: 'Sales Team',
    description: 'Experienced professionals who understand government procurement processes and can guide you through complex requirements.'
  },
  {
    name: 'Support Team',
    description: 'Dedicated support staff available to help with installation, configuration, and ongoing maintenance of your systems.'
  }
]

export default function AboutPage() {
  return (
    <Layout>
      <div className="bg-white">
        {/* Hero Section */}
        <div className="bg-gradient-to-r from-primary to-secondary text-white py-20">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="text-center">
              <h1 className="text-4xl md:text-5xl font-bold mb-4">
                About Pubuild
              </h1>
              <p className="text-xl text-blue-100 max-w-3xl mx-auto">
                Your trusted partner for professional IT hardware solutions and 
                government contracting services. We deliver enterprise-grade 
                infrastructure that meets the highest standards of security and compliance.
              </p>
            </div>
          </div>
        </div>

        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
          {/* Mission & Vision */}
          <div className="mb-16">
            <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
              <div>
                <h2 className="text-3xl font-bold text-gray-900 mb-6">Our Mission</h2>
                <p className="text-lg text-gray-700 mb-6">
                  To provide government agencies, enterprises, and organizations with 
                  reliable, secure, and compliant IT hardware solutions that enable 
                  mission-critical operations and drive digital transformation.
                </p>
                <p className="text-lg text-gray-700">
                  We are committed to understanding our clients' unique requirements 
                  and delivering customized solutions that exceed expectations while 
                  maintaining the highest standards of security and compliance.
                </p>
              </div>
              <div className="bg-gray-50 rounded-xl p-8">
                <h3 className="text-2xl font-bold text-gray-900 mb-4">Our Vision</h3>
                <p className="text-gray-700 mb-4">
                  To be the leading provider of government-grade IT hardware solutions, 
                  recognized for our expertise in compliance, security, and customer service.
                </p>
                <ul className="space-y-2">
                  <li className="flex items-center">
                    <CheckCircle className="h-5 w-5 text-green-500 mr-3" />
                    <span className="text-gray-700">Trusted by government agencies</span>
                  </li>
                  <li className="flex items-center">
                    <CheckCircle className="h-5 w-5 text-green-500 mr-3" />
                    <span className="text-gray-700">Compliance-first approach</span>
                  </li>
                  <li className="flex items-center">
                    <CheckCircle className="h-5 w-5 text-green-500 mr-3" />
                    <span className="text-gray-700">Innovation and reliability</span>
                  </li>
                </ul>
              </div>
            </div>
          </div>

          {/* Values */}
          <div className="mb-16">
            <h2 className="text-3xl font-bold text-gray-900 mb-8 text-center">
              Our Values
            </h2>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
              {values.map((value, index) => {
                const Icon = value.icon
                return (
                  <div key={index} className="text-center">
                    <div className="bg-primary/10 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                      <Icon className="h-8 w-8 text-primary" />
                    </div>
                    <h3 className="text-lg font-semibold text-gray-900 mb-2">
                      {value.title}
                    </h3>
                    <p className="text-gray-600 text-sm">
                      {value.description}
                    </p>
                  </div>
                )
              })}
            </div>
          </div>

          {/* Team */}
          <div className="mb-16">
            <h2 className="text-3xl font-bold text-gray-900 mb-8 text-center">
              Our Team
            </h2>
            <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
              {team.map((member, index) => (
                <div key={index} className="bg-gray-50 rounded-lg p-6">
                  <h3 className="text-xl font-semibold text-gray-900 mb-3">
                    {member.name}
                  </h3>
                  <p className="text-gray-600">
                    {member.description}
                  </p>
                </div>
              ))}
            </div>
          </div>

          {/* Capabilities */}
          <div className="mb-16">
            <h2 className="text-3xl font-bold text-gray-900 mb-8 text-center">
              Our Capabilities
            </h2>
            <div className="bg-gray-50 rounded-xl p-8">
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <div>
                  <h3 className="text-lg font-semibold text-gray-900 mb-3">Government Contracting</h3>
                  <ul className="space-y-2 text-sm text-gray-700">
                    <li>• NAICS code compliance</li>
                    <li>• RFP response and support</li>
                    <li>• Security clearance requirements</li>
                    <li>• Export control compliance</li>
                  </ul>
                </div>
                <div>
                  <h3 className="text-lg font-semibold text-gray-900 mb-3">IT Hardware Solutions</h3>
                  <ul className="space-y-2 text-sm text-gray-700">
                    <li>• Servers and workstations</li>
                    <li>• Networking equipment</li>
                    <li>• Storage solutions</li>
                    <li>• Custom configurations</li>
                  </ul>
                </div>
                <div>
                  <h3 className="text-lg font-semibold text-gray-900 mb-3">Support Services</h3>
                  <ul className="space-y-2 text-sm text-gray-700">
                    <li>• 24/7 technical support</li>
                    <li>• Installation and setup</li>
                    <li>• Maintenance and upgrades</li>
                    <li>• Training and documentation</li>
                  </ul>
                </div>
              </div>
            </div>
          </div>

          {/* Contact CTA */}
          <div className="bg-primary text-white rounded-2xl p-8 lg:p-12 text-center">
            <h2 className="text-3xl font-bold mb-4">
              Ready to Work With Us?
            </h2>
            <p className="text-xl text-blue-100 mb-8 max-w-3xl mx-auto">
              Contact our team to discuss your IT hardware requirements and discover 
              how we can help you achieve your mission-critical objectives.
            </p>
            <div className="flex flex-col sm:flex-row gap-4 justify-center">
              <a
                href="/contact"
                className="inline-flex items-center justify-center px-8 py-3 border border-transparent text-base font-medium rounded-md text-primary bg-white hover:bg-gray-100 transition-colors duration-200"
              >
                Contact Us
              </a>
              <a
                href="/government"
                className="inline-flex items-center justify-center px-8 py-3 border border-white text-base font-medium rounded-md text-white hover:bg-white hover:text-primary transition-colors duration-200"
              >
                Government Services
              </a>
            </div>
          </div>
        </div>
      </div>
    </Layout>
  )
}