import { Shield, Lock, Award, Clock, CheckCircle } from 'lucide-react'

const trustFeatures = [
  {
    icon: Shield,
    title: 'Security First',
    description: 'All products meet federal security standards and compliance requirements'
  },
  {
    icon: Lock,
    title: 'Data Protection',
    description: 'Privacy-first approach with secure data handling and storage'
  },
  {
    icon: Award,
    title: 'Quality Assured',
    description: 'Enterprise-grade hardware from trusted manufacturers'
  },
  {
    icon: Clock,
    title: 'Fast Delivery',
    description: 'Quick turnaround times for government and enterprise orders'
  },
  {
    icon: CheckCircle,
    title: 'Compliance Ready',
    description: 'Export controls, certifications, and regulatory compliance'
  }
]

export default function TrustBadges() {
  return (
    <section className="py-20 bg-gray-50">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="text-center mb-16">
          <h2 className="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
            Why Government Agencies Trust Pubuild
          </h2>
          <p className="text-xl text-gray-600 max-w-3xl mx-auto">
            We understand the unique requirements of government IT procurement 
            and deliver solutions that meet the highest standards of security, 
            compliance, and reliability.
          </p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8">
          {trustFeatures.map((feature, index) => {
            const Icon = feature.icon
            return (
              <div
                key={index}
                className="text-center group hover:transform hover:scale-105 transition-transform duration-300"
              >
                <div className="bg-white rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4 shadow-lg group-hover:shadow-xl transition-shadow duration-300">
                  <Icon className="h-10 w-10 text-primary" />
                </div>
                <h3 className="text-lg font-semibold text-gray-900 mb-2">
                  {feature.title}
                </h3>
                <p className="text-gray-600 text-sm">
                  {feature.description}
                </p>
              </div>
            )
          })}
        </div>

        <div className="mt-16 bg-white rounded-2xl shadow-lg p-8 lg:p-12">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
            <div>
              <h3 className="text-2xl font-bold text-gray-900 mb-4">
                Ready to Get Started?
              </h3>
              <p className="text-lg text-gray-600 mb-6">
                Contact our government contracting team to discuss your IT hardware 
                requirements and learn how we can support your agency's mission.
              </p>
              <div className="space-y-4">
                <div className="flex items-center">
                  <CheckCircle className="h-5 w-5 text-green-500 mr-3" />
                  <span className="text-gray-700">Free consultation and needs assessment</span>
                </div>
                <div className="flex items-center">
                  <CheckCircle className="h-5 w-5 text-green-500 mr-3" />
                  <span className="text-gray-700">Custom pricing for government contracts</span>
                </div>
                <div className="flex items-center">
                  <CheckCircle className="h-5 w-5 text-green-500 mr-3" />
                  <span className="text-gray-700">Compliance documentation included</span>
                </div>
              </div>
            </div>
            
            <div className="bg-gray-50 rounded-xl p-6">
              <h4 className="text-lg font-semibold text-gray-900 mb-4">
                Contact Information
              </h4>
              <div className="space-y-3">
                <div>
                  <div className="text-sm font-medium text-gray-500">Phone</div>
                  <div className="text-lg text-gray-900">800-474-1388</div>
                </div>
                <div>
                  <div className="text-sm font-medium text-gray-500">Email</div>
                  <div className="text-lg text-gray-900">sales@pubuild.com</div>
                </div>
                <div>
                  <div className="text-sm font-medium text-gray-500">Response Time</div>
                  <div className="text-lg text-gray-900">Within 24 hours</div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  )
}