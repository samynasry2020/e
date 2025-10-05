import Layout from '@/components/layout/layout'
import { Brain, Building2, Users, ArrowRight } from 'lucide-react'
import Link from 'next/link'
import { Button } from '@/components/ui/button'

const solutions = [
  {
    name: 'AI/HPC Infrastructure',
    description: 'High-performance computing solutions for artificial intelligence and machine learning workloads.',
    href: '/solutions/ai-hpc',
    icon: Brain,
    features: [
      'GPU Clusters for AI Training',
      'High-Speed Interconnects',
      'Scalable Storage Solutions',
      'Optimized Cooling Systems',
      'Distributed Computing',
      'Model Training & Inference'
    ],
    benefits: [
      'Faster AI model training',
      'Scalable infrastructure',
      'Cost-effective solutions',
      'Expert support'
    ]
  },
  {
    name: 'Data Center Builds',
    description: 'Complete data center design, implementation, and management services.',
    href: '/solutions/data-center',
    icon: Building2,
    features: [
      'Tier III/IV Design Standards',
      'Power & Cooling Infrastructure',
      'Security & Compliance',
      '24/7 Monitoring & Support',
      'Disaster Recovery',
      'Green Energy Solutions'
    ],
    benefits: [
      'Reduced downtime',
      'Energy efficiency',
      'Compliance ready',
      'Future-proof design'
    ]
  },
  {
    name: 'Workstation Fleets',
    description: 'Deploy and manage large-scale workstation environments for government agencies.',
    href: '/solutions/workstation-fleets',
    icon: Users,
    features: [
      'Bulk Procurement Programs',
      'Standardized Configurations',
      'Asset Management',
      'Lifecycle Support',
      'Remote Management',
      'Security Hardening'
    ],
    benefits: [
      'Consistent performance',
      'Reduced IT overhead',
      'Centralized management',
      'Cost savings'
    ]
  }
]

export default function SolutionsPage() {
  return (
    <Layout>
      <div className="bg-white">
        {/* Hero Section */}
        <div className="bg-gradient-to-r from-primary to-secondary text-white py-20">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="text-center">
              <h1 className="text-4xl md:text-5xl font-bold mb-4">
                Enterprise IT Solutions
              </h1>
              <p className="text-xl text-blue-100 max-w-3xl mx-auto">
                Comprehensive IT infrastructure solutions designed for government 
                agencies, enterprises, and mission-critical applications. We deliver 
                end-to-end solutions that meet your specific requirements.
              </p>
            </div>
          </div>
        </div>

        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
          {/* Solutions Grid */}
          <div className="mb-16">
            <h2 className="text-3xl font-bold text-gray-900 mb-8 text-center">
              Our Solution Categories
            </h2>
            <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
              {solutions.map((solution) => {
                const Icon = solution.icon
                return (
                  <div
                    key={solution.name}
                    className="bg-gray-50 rounded-xl p-8 hover:shadow-lg transition-shadow duration-300 group"
                  >
                    <div className="flex items-center mb-6">
                      <div className="p-4 bg-primary text-white rounded-lg group-hover:scale-110 transition-transform duration-300">
                        <Icon className="h-8 w-8" />
                      </div>
                    </div>
                    
                    <h3 className="text-2xl font-bold text-gray-900 mb-4">
                      {solution.name}
                    </h3>
                    
                    <p className="text-gray-600 mb-6 leading-relaxed">
                      {solution.description}
                    </p>

                    <div className="mb-6">
                      <h4 className="font-semibold text-gray-700 mb-3">Key Features:</h4>
                      <ul className="space-y-2">
                        {solution.features.map((feature, index) => (
                          <li key={index} className="flex items-start">
                            <div className="w-2 h-2 bg-primary rounded-full mt-2 mr-3 flex-shrink-0"></div>
                            <span className="text-gray-700 text-sm">{feature}</span>
                          </li>
                        ))}
                      </ul>
                    </div>

                    <div className="mb-8">
                      <h4 className="font-semibold text-gray-700 mb-3">Benefits:</h4>
                      <ul className="space-y-2">
                        {solution.benefits.map((benefit, index) => (
                          <li key={index} className="flex items-start">
                            <div className="w-2 h-2 bg-secondary rounded-full mt-2 mr-3 flex-shrink-0"></div>
                            <span className="text-gray-700 text-sm">{benefit}</span>
                          </li>
                        ))}
                      </ul>
                    </div>

                    <Button asChild className="w-full group-hover:bg-primary group-hover:text-white transition-colors duration-300">
                      <Link href={solution.href} className="flex items-center justify-center">
                        Learn More
                        <ArrowRight className="ml-2 h-4 w-4" />
                      </Link>
                    </Button>
                  </div>
                )
              })}
            </div>
          </div>

          {/* Process Section */}
          <div className="mb-16">
            <h2 className="text-3xl font-bold text-gray-900 mb-8 text-center">
              Our Solution Process
            </h2>
            <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
              <div className="text-center">
                <div className="bg-primary text-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4 text-2xl font-bold">
                  1
                </div>
                <h3 className="text-lg font-semibold text-gray-900 mb-2">Assessment</h3>
                <p className="text-gray-600 text-sm">
                  Analyze your current infrastructure and requirements
                </p>
              </div>
              
              <div className="text-center">
                <div className="bg-primary text-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4 text-2xl font-bold">
                  2
                </div>
                <h3 className="text-lg font-semibold text-gray-900 mb-2">Design</h3>
                <p className="text-gray-600 text-sm">
                  Create a custom solution architecture for your needs
                </p>
              </div>
              
              <div className="text-center">
                <div className="bg-primary text-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4 text-2xl font-bold">
                  3
                </div>
                <h3 className="text-lg font-semibold text-gray-900 mb-2">Implementation</h3>
                <p className="text-gray-600 text-sm">
                  Deploy and configure your new infrastructure
                </p>
              </div>
              
              <div className="text-center">
                <div className="bg-primary text-white rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4 text-2xl font-bold">
                  4
                </div>
                <h3 className="text-lg font-semibold text-gray-900 mb-2">Support</h3>
                <p className="text-gray-600 text-sm">
                  Ongoing maintenance and support services
                </p>
              </div>
            </div>
          </div>

          {/* CTA Section */}
          <div className="bg-primary text-white rounded-2xl p-8 lg:p-12">
            <div className="text-center">
              <h2 className="text-3xl font-bold mb-4">
                Ready to Transform Your IT Infrastructure?
              </h2>
              <p className="text-xl text-blue-100 mb-8 max-w-3xl mx-auto">
                Contact our solution architects to discuss your requirements 
                and discover how we can help optimize your IT infrastructure.
              </p>
              <div className="flex flex-col sm:flex-row gap-4 justify-center">
                <Button asChild size="lg" className="bg-white text-primary hover:bg-gray-100">
                  <Link href="/contact">
                    Discuss Your Project
                  </Link>
                </Button>
                <Button asChild size="lg" variant="outline" className="border-white text-white hover:bg-white hover:text-primary">
                  <Link href="/government">
                    Government Solutions
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