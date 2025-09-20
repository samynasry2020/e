import Link from 'next/link'
import { 
  Server, 
  Shield, 
  Cpu, 
  HardDrive, 
  Network, 
  Settings, 
  Lock, 
  Zap,
  CheckCircle,
  ArrowRight,
  Clock,
  Award,
  Users
} from 'lucide-react'

export default function ServicesPage() {
  const mainServices = [
    {
      icon: Server,
      title: "Custom Server Manufacturing",
      description: "Built-to-specification servers designed for government requirements",
      features: [
        "Military-grade components",
        "Custom configurations",
        "Secure boot processes",
        "Government compliance",
        "Extended warranties"
      ]
    },
    {
      icon: Settings,
      title: "Server Setup & Configuration",
      description: "Professional installation and configuration services",
      features: [
        "Secure OS installation",
        "Network configuration",
        "Security hardening",
        "Performance optimization",
        "Documentation provided"
      ]
    },
    {
      icon: Shield,
      title: "Security Implementation",
      description: "Advanced security measures for government infrastructure",
      features: [
        "Encryption at rest",
        "Multi-factor authentication",
        "Intrusion detection",
        "Compliance auditing",
        "Security monitoring"
      ]
    },
    {
      icon: Network,
      title: "Infrastructure Design",
      description: "Complete server infrastructure planning and design",
      features: [
        "Scalability planning",
        "Redundancy design",
        "Network architecture",
        "Disaster recovery",
        "Capacity planning"
      ]
    }
  ]

  const technicalSpecs = [
    {
      category: "Processing Power",
      icon: Cpu,
      specs: [
        "Intel Xeon Scalable processors",
        "AMD EPYC server processors",
        "Multi-core configurations",
        "High-performance computing"
      ]
    },
    {
      category: "Storage Solutions",
      icon: HardDrive,
      specs: [
        "Enterprise SSD arrays",
        "High-capacity HDDs",
        "RAID configurations",
        "Encrypted storage"
      ]
    },
    {
      category: "Security Features",
      icon: Lock,
      specs: [
        "TPM 2.0 modules",
        "Secure boot enabled",
        "Hardware encryption",
        "Tamper detection"
      ]
    },
    {
      category: "Performance",
      icon: Zap,
      specs: [
        "High-speed networking",
        "Low-latency memory",
        "Optimized cooling",
        "Power efficiency"
      ]
    }
  ]

  const serviceProcess = [
    {
      step: "1",
      title: "Requirements Analysis",
      description: "We analyze your government agency's specific needs and compliance requirements."
    },
    {
      step: "2",
      title: "Custom Design",
      description: "Our engineers design a tailored solution that meets all specifications."
    },
    {
      step: "3",
      title: "Manufacturing",
      description: "Servers are built using premium components in our secure facilities."
    },
    {
      step: "4",
      title: "Testing & QA",
      description: "Rigorous testing ensures reliability and security standards are met."
    },
    {
      step: "5",
      title: "Deployment",
      description: "Professional installation and configuration at your facility."
    },
    {
      step: "6",
      title: "Support",
      description: "Ongoing maintenance and 24/7 support for government clients."
    }
  ]

  return (
    <div className="min-h-screen">
      {/* Hero Section */}
      <section className="bg-gradient-to-r from-primary-600 to-primary-800 text-white py-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center">
            <h1 className="text-4xl md:text-5xl font-bold mb-6">
              Government Server Solutions
            </h1>
            <p className="text-xl md:text-2xl mb-8 max-w-3xl mx-auto opacity-90">
              Comprehensive server manufacturing, setup, and design services 
              tailored exclusively for US Government agencies.
            </p>
            <div className="flex justify-center space-x-4">
              <div className="flex items-center space-x-2 bg-white/10 px-4 py-2 rounded-lg">
                <Award className="h-5 w-5" />
                <span>Government Certified</span>
              </div>
              <div className="flex items-center space-x-2 bg-white/10 px-4 py-2 rounded-lg">
                <Shield className="h-5 w-5" />
                <span>Security Compliant</span>
              </div>
              <div className="flex items-center space-x-2 bg-white/10 px-4 py-2 rounded-lg">
                <Clock className="h-5 w-5" />
                <span>24/7 Support</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Main Services */}
      <section className="py-20 bg-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-16">
            <h2 className="text-3xl md:text-4xl font-bold text-secondary-900 mb-4">
              Our Core Services
            </h2>
            <p className="text-xl text-secondary-600 max-w-3xl mx-auto">
              End-to-end server solutions designed specifically for government operations
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
            {mainServices.map((service, index) => (
              <div key={index} className="bg-secondary-50 p-8 rounded-lg card-hover">
                <div className="flex items-center mb-6">
                  <div className="flex items-center justify-center w-12 h-12 bg-primary-100 rounded-lg mr-4">
                    <service.icon className="h-6 w-6 text-primary-600" />
                  </div>
                  <h3 className="text-2xl font-bold text-secondary-900">
                    {service.title}
                  </h3>
                </div>
                <p className="text-secondary-600 mb-6">
                  {service.description}
                </p>
                <ul className="space-y-3">
                  {service.features.map((feature, idx) => (
                    <li key={idx} className="flex items-center space-x-3">
                      <CheckCircle className="h-5 w-5 text-green-500 flex-shrink-0" />
                      <span className="text-secondary-700">{feature}</span>
                    </li>
                  ))}
                </ul>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Technical Specifications */}
      <section className="py-20 bg-secondary-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-16">
            <h2 className="text-3xl md:text-4xl font-bold text-secondary-900 mb-4">
              Technical Specifications
            </h2>
            <p className="text-xl text-secondary-600 max-w-3xl mx-auto">
              Enterprise-grade components meeting the highest government standards
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            {technicalSpecs.map((spec, index) => (
              <div key={index} className="bg-white p-6 rounded-lg shadow-md">
                <div className="flex items-center justify-center w-12 h-12 bg-primary-100 rounded-lg mb-4">
                  <spec.icon className="h-6 w-6 text-primary-600" />
                </div>
                <h3 className="text-lg font-semibold text-secondary-900 mb-4">
                  {spec.category}
                </h3>
                <ul className="space-y-2">
                  {spec.specs.map((item, idx) => (
                    <li key={idx} className="text-secondary-600 text-sm">
                      • {item}
                    </li>
                  ))}
                </ul>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Service Process */}
      <section className="py-20 bg-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-16">
            <h2 className="text-3xl md:text-4xl font-bold text-secondary-900 mb-4">
              Our Service Process
            </h2>
            <p className="text-xl text-secondary-600 max-w-3xl mx-auto">
              A systematic approach ensuring quality and compliance at every step
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            {serviceProcess.map((process, index) => (
              <div key={index} className="relative">
                <div className="flex items-start space-x-4">
                  <div className="flex items-center justify-center w-12 h-12 bg-primary-600 text-white rounded-full font-bold text-lg flex-shrink-0">
                    {process.step}
                  </div>
                  <div>
                    <h3 className="text-xl font-semibold text-secondary-900 mb-2">
                      {process.title}
                    </h3>
                    <p className="text-secondary-600">
                      {process.description}
                    </p>
                  </div>
                </div>
                {index < serviceProcess.length - 1 && (
                  <div className="hidden lg:block absolute top-6 left-full w-8 h-0.5 bg-primary-200 transform translate-x-4"></div>
                )}
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Government Compliance */}
      <section className="py-20 bg-secondary-900 text-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
              <h2 className="text-3xl md:text-4xl font-bold mb-6">
                Government Compliance & Certifications
              </h2>
              <p className="text-lg text-secondary-300 mb-8">
                All our solutions meet or exceed government standards for security, 
                reliability, and performance. We maintain the highest levels of 
                compliance for federal contracts.
              </p>
              
              <div className="grid grid-cols-2 gap-6">
                <div className="space-y-4">
                  <h4 className="font-semibold text-primary-400">Security Standards</h4>
                  <ul className="space-y-2 text-secondary-300 text-sm">
                    <li>• FIPS 140-2 Compliance</li>
                    <li>• Common Criteria EAL4+</li>
                    <li>• NIST Cybersecurity Framework</li>
                    <li>• FedRAMP Authorization</li>
                  </ul>
                </div>
                <div className="space-y-4">
                  <h4 className="font-semibold text-primary-400">Quality Certifications</h4>
                  <ul className="space-y-2 text-secondary-300 text-sm">
                    <li>• ISO 9001:2015</li>
                    <li>• ISO 27001:2013</li>
                    <li>• SOC 2 Type II</li>
                    <li>• GSA Schedule Holder</li>
                  </ul>
                </div>
              </div>
            </div>
            
            <div className="bg-primary-600 p-8 rounded-lg">
              <h3 className="text-2xl font-bold mb-6">Why Choose PUBUILD?</h3>
              <div className="space-y-4">
                <div className="flex items-start space-x-3">
                  <Users className="h-6 w-6 text-white flex-shrink-0 mt-1" />
                  <div>
                    <h4 className="font-semibold mb-1">Government Exclusive</h4>
                    <p className="text-primary-100 text-sm">
                      100% focus on government clients ensures specialized expertise
                    </p>
                  </div>
                </div>
                <div className="flex items-start space-x-3">
                  <Shield className="h-6 w-6 text-white flex-shrink-0 mt-1" />
                  <div>
                    <h4 className="font-semibold mb-1">Security First</h4>
                    <p className="text-primary-100 text-sm">
                      Built-in security measures from design to deployment
                    </p>
                  </div>
                </div>
                <div className="flex items-start space-x-3">
                  <Award className="h-6 w-6 text-white flex-shrink-0 mt-1" />
                  <div>
                    <h4 className="font-semibold mb-1">Proven Track Record</h4>
                    <p className="text-primary-100 text-sm">
                      15+ years serving federal agencies with 99.9% uptime
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* CTA Section */}
      <section className="py-20 bg-primary-600 text-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <h2 className="text-3xl md:text-4xl font-bold mb-6">
            Ready to Discuss Your Requirements?
          </h2>
          <p className="text-xl mb-8 max-w-2xl mx-auto opacity-90">
            Contact our government solutions team to learn how we can support 
            your agency's server infrastructure needs.
          </p>
          <div className="flex flex-col sm:flex-row gap-4 justify-center">
            <Link
              href="/contact"
              className="inline-flex items-center px-8 py-3 bg-white text-primary-600 font-semibold rounded-lg hover:bg-secondary-100 transition-colors"
            >
              Request Consultation
              <ArrowRight className="ml-2 h-5 w-5" />
            </Link>
            <Link
              href="/government"
              className="inline-flex items-center px-8 py-3 border-2 border-white text-white font-semibold rounded-lg hover:bg-white hover:text-primary-600 transition-colors"
            >
              Government Solutions
            </Link>
          </div>
        </div>
      </section>
    </div>
  )
}