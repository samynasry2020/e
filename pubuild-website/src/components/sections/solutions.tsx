import Link from 'next/link'
import { Button } from '@/components/ui/button'
import { Brain, Building2, Users, ArrowRight } from 'lucide-react'

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
      'Optimized Cooling Systems'
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
      '24/7 Monitoring & Support'
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
      'Lifecycle Support'
    ]
  }
]

export default function Solutions() {
  return (
    <section className="py-20 bg-white">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="text-center mb-16">
          <h2 className="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
            Enterprise Solutions
          </h2>
          <p className="text-xl text-gray-600 max-w-3xl mx-auto">
            Comprehensive IT infrastructure solutions designed for government agencies, 
            enterprises, and mission-critical applications.
          </p>
        </div>

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

                <ul className="space-y-3 mb-8">
                  {solution.features.map((feature, index) => (
                    <li key={index} className="flex items-start">
                      <div className="w-2 h-2 bg-primary rounded-full mt-2 mr-3 flex-shrink-0"></div>
                      <span className="text-gray-700">{feature}</span>
                    </li>
                  ))}
                </ul>

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

        <div className="mt-16 bg-primary text-white rounded-2xl p-8 lg:p-12">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-8 items-center">
            <div>
              <h3 className="text-3xl font-bold mb-4">
                Need a Custom Solution?
              </h3>
              <p className="text-xl text-blue-100 mb-6">
                Our team of experts can design and implement tailored IT infrastructure 
                solutions to meet your specific requirements and compliance needs.
              </p>
              <div className="flex flex-col sm:flex-row gap-4">
                <Button asChild size="lg" className="bg-white text-primary hover:bg-gray-100">
                  <Link href="/contact">
                    Discuss Your Project
                  </Link>
                </Button>
                <Button asChild size="lg" variant="outline" className="border-white text-white hover:bg-white hover:text-primary">
                  <Link href="/government">
                    Government Contracting
                  </Link>
                </Button>
              </div>
            </div>
            <div className="bg-white/10 backdrop-blur-sm rounded-xl p-6">
              <h4 className="text-xl font-semibold mb-4">What We Deliver</h4>
              <ul className="space-y-2">
                <li className="flex items-center">
                  <div className="w-2 h-2 bg-blue-200 rounded-full mr-3"></div>
                  <span>Custom hardware configurations</span>
                </li>
                <li className="flex items-center">
                  <div className="w-2 h-2 bg-blue-200 rounded-full mr-3"></div>
                  <span>Compliance documentation</span>
                </li>
                <li className="flex items-center">
                  <div className="w-2 h-2 bg-blue-200 rounded-full mr-3"></div>
                  <span>Installation & setup services</span>
                </li>
                <li className="flex items-center">
                  <div className="w-2 h-2 bg-blue-200 rounded-full mr-3"></div>
                  <span>Ongoing support & maintenance</span>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </section>
  )
}