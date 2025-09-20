import Link from 'next/link'
import { 
  Shield, 
  Building2, 
  Users, 
  Lock, 
  Award, 
  FileText,
  CheckCircle,
  ArrowRight,
  Globe,
  Server,
  Zap,
  Clock
} from 'lucide-react'

export default function GovernmentPage() {
  const agencies = [
    {
      name: "Department of Defense",
      description: "Secure server infrastructure for defense operations",
      icon: Shield,
      services: ["Classified systems", "Tactical servers", "Secure communications"]
    },
    {
      name: "Department of Homeland Security",
      description: "Critical infrastructure protection and monitoring",
      icon: Building2,
      services: ["Border security systems", "Emergency response", "Threat detection"]
    },
    {
      name: "Intelligence Agencies",
      description: "High-security computing for intelligence operations",
      icon: Lock,
      services: ["Secure processing", "Data analytics", "Encrypted storage"]
    },
    {
      name: "Federal Law Enforcement",
      description: "Reliable systems for law enforcement operations",
      icon: Users,
      services: ["Case management", "Evidence storage", "Communication systems"]
    }
  ]

  const certifications = [
    {
      name: "FedRAMP",
      description: "Federal Risk and Authorization Management Program",
      level: "High Impact Level"
    },
    {
      name: "FIPS 140-2",
      description: "Federal Information Processing Standards",
      level: "Level 4 Certified"
    },
    {
      name: "Common Criteria",
      description: "International security evaluation standard",
      level: "EAL4+ Certified"
    },
    {
      name: "NIST",
      description: "National Institute of Standards and Technology",
      level: "Cybersecurity Framework"
    },
    {
      name: "GSA Schedule",
      description: "General Services Administration",
      level: "Contract Holder"
    },
    {
      name: "ISO 27001",
      description: "Information Security Management",
      level: "Certified"
    }
  ]

  const governmentBenefits = [
    {
      title: "Exclusive Government Focus",
      description: "100% dedicated to federal agencies - no commercial clients",
      icon: Building2
    },
    {
      title: "Security Clearance Team",
      description: "Staff with appropriate security clearances for classified work",
      icon: Lock
    },
    {
      title: "Compliance Expertise",
      description: "Deep knowledge of federal regulations and requirements",
      icon: FileText
    },
    {
      title: "24/7 Government Support",
      description: "Round-the-clock support specifically for government operations",
      icon: Clock
    },
    {
      title: "Rapid Response",
      description: "Priority deployment for critical government needs",
      icon: Zap
    },
    {
      title: "Long-term Partnerships",
      description: "Established relationships with multiple federal agencies",
      icon: Users
    }
  ]

  const contractVehicles = [
    {
      name: "GSA Multiple Award Schedule",
      number: "GS-35F-0119Y",
      description: "Information Technology Professional Services"
    },
    {
      name: "SEWP VI",
      number: "NNG15SC03B",
      description: "Solutions for Enterprise-Wide Procurement"
    },
    {
      name: "CIO-SP3",
      number: "HHSN316201200012W",
      description: "Chief Information Officer-Solutions and Partners 3"
    },
    {
      name: "OASIS",
      number: "GS-00F-009CA",
      description: "One Acquisition Solution for Integrated Services"
    }
  ]

  return (
    <div className="min-h-screen">
      {/* Hero Section */}
      <section className="bg-gradient-to-r from-secondary-900 to-secondary-800 text-white py-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center">
            <div className="flex justify-center mb-6">
              <div className="flex items-center space-x-2 bg-white/10 px-6 py-2 rounded-full">
                <Shield className="h-6 w-6 text-primary-400" />
                <span className="font-semibold">Government Exclusive</span>
              </div>
            </div>
            <h1 className="text-4xl md:text-5xl font-bold mb-6">
              Serving America's Government Agencies
            </h1>
            <p className="text-xl md:text-2xl mb-8 max-w-4xl mx-auto opacity-90">
              PUBUILD TECHNOLOGIES INC. provides specialized server solutions exclusively 
              for US Government agencies, federal departments, and authorized contractors.
            </p>
            <div className="flex flex-col sm:flex-row gap-4 justify-center">
              <Link
                href="/contact"
                className="inline-flex items-center px-8 py-3 bg-primary-600 text-white font-semibold rounded-lg hover:bg-primary-700 transition-colors"
              >
                Contact Government Sales
                <ArrowRight className="ml-2 h-5 w-5" />
              </Link>
              <Link
                href="/bidding"
                className="inline-flex items-center px-8 py-3 border-2 border-white text-white font-semibold rounded-lg hover:bg-white hover:text-secondary-900 transition-colors"
              >
                View Active Bids
              </Link>
            </div>
          </div>
        </div>
      </section>

      {/* Government Agencies We Serve */}
      <section className="py-20 bg-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-16">
            <h2 className="text-3xl md:text-4xl font-bold text-secondary-900 mb-4">
              Government Agencies We Serve
            </h2>
            <p className="text-xl text-secondary-600 max-w-3xl mx-auto">
              Trusted by federal agencies across all branches of government
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
            {agencies.map((agency, index) => (
              <div key={index} className="bg-secondary-50 p-8 rounded-lg card-hover">
                <div className="flex items-center mb-6">
                  <div className="flex items-center justify-center w-12 h-12 bg-primary-100 rounded-lg mr-4">
                    <agency.icon className="h-6 w-6 text-primary-600" />
                  </div>
                  <h3 className="text-xl font-bold text-secondary-900">
                    {agency.name}
                  </h3>
                </div>
                <p className="text-secondary-600 mb-6">
                  {agency.description}
                </p>
                <div>
                  <h4 className="font-semibold text-secondary-900 mb-3">Specialized Services:</h4>
                  <ul className="space-y-2">
                    {agency.services.map((service, idx) => (
                      <li key={idx} className="flex items-center space-x-3">
                        <CheckCircle className="h-4 w-4 text-green-500 flex-shrink-0" />
                        <span className="text-secondary-700 text-sm">{service}</span>
                      </li>
                    ))}
                  </ul>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Government Benefits */}
      <section className="py-20 bg-secondary-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-16">
            <h2 className="text-3xl md:text-4xl font-bold text-secondary-900 mb-4">
              Why Government Agencies Choose PUBUILD
            </h2>
            <p className="text-xl text-secondary-600 max-w-3xl mx-auto">
              Specialized expertise and exclusive focus on government requirements
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            {governmentBenefits.map((benefit, index) => (
              <div key={index} className="bg-white p-6 rounded-lg shadow-md card-hover">
                <div className="flex items-center justify-center w-12 h-12 bg-primary-100 rounded-lg mb-4">
                  <benefit.icon className="h-6 w-6 text-primary-600" />
                </div>
                <h3 className="text-lg font-semibold text-secondary-900 mb-3">
                  {benefit.title}
                </h3>
                <p className="text-secondary-600">
                  {benefit.description}
                </p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Certifications & Compliance */}
      <section className="py-20 bg-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-16">
            <h2 className="text-3xl md:text-4xl font-bold text-secondary-900 mb-4">
              Certifications & Compliance
            </h2>
            <p className="text-xl text-secondary-600 max-w-3xl mx-auto">
              Meeting the highest standards for government security and compliance
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            {certifications.map((cert, index) => (
              <div key={index} className="bg-gradient-to-br from-primary-50 to-primary-100 p-6 rounded-lg border border-primary-200">
                <div className="flex items-center justify-center w-12 h-12 bg-primary-600 text-white rounded-lg mb-4">
                  <Award className="h-6 w-6" />
                </div>
                <h3 className="text-lg font-bold text-primary-800 mb-2">
                  {cert.name}
                </h3>
                <p className="text-secondary-600 text-sm mb-2">
                  {cert.description}
                </p>
                <div className="inline-block bg-primary-600 text-white text-xs px-3 py-1 rounded-full">
                  {cert.level}
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Contract Vehicles */}
      <section className="py-20 bg-secondary-900 text-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-16">
            <h2 className="text-3xl md:text-4xl font-bold mb-4">
              Government Contract Vehicles
            </h2>
            <p className="text-xl text-secondary-300 max-w-3xl mx-auto">
              Pre-approved contract vehicles for streamlined procurement
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
            {contractVehicles.map((contract, index) => (
              <div key={index} className="bg-secondary-800 p-6 rounded-lg">
                <div className="flex items-start justify-between mb-4">
                  <h3 className="text-xl font-bold text-primary-400">
                    {contract.name}
                  </h3>
                  <div className="bg-primary-600 text-white text-sm px-3 py-1 rounded-full">
                    Active
                  </div>
                </div>
                <p className="text-secondary-300 text-sm mb-3">
                  Contract Number: {contract.number}
                </p>
                <p className="text-secondary-200">
                  {contract.description}
                </p>
              </div>
            ))}
          </div>

          <div className="text-center mt-12">
            <p className="text-secondary-300 mb-6">
              Need help with procurement? Our government contracts team can assist.
            </p>
            <Link
              href="/contact"
              className="inline-flex items-center px-8 py-3 bg-primary-600 text-white font-semibold rounded-lg hover:bg-primary-700 transition-colors"
            >
              Contact Contracts Team
              <ArrowRight className="ml-2 h-5 w-5" />
            </Link>
          </div>
        </div>
      </section>

      {/* Security & Clearance */}
      <section className="py-20 bg-primary-600 text-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
              <h2 className="text-3xl md:text-4xl font-bold mb-6">
                Security Clearance & Classified Work
              </h2>
              <p className="text-lg text-primary-100 mb-8">
                Our team maintains the necessary security clearances to work on 
                classified government projects. We understand the unique requirements 
                of secure government operations.
              </p>
              
              <div className="space-y-6">
                <div className="flex items-start space-x-4">
                  <div className="flex items-center justify-center w-8 h-8 bg-white/20 rounded-full flex-shrink-0 mt-1">
                    <Lock className="h-4 w-4" />
                  </div>
                  <div>
                    <h4 className="font-semibold mb-2">Security Clearances</h4>
                    <p className="text-primary-100 text-sm">
                      Team members hold Secret and Top Secret clearances as required
                    </p>
                  </div>
                </div>
                <div className="flex items-start space-x-4">
                  <div className="flex items-center justify-center w-8 h-8 bg-white/20 rounded-full flex-shrink-0 mt-1">
                    <Shield className="h-4 w-4" />
                  </div>
                  <div>
                    <h4 className="font-semibold mb-2">Secure Facilities</h4>
                    <p className="text-primary-100 text-sm">
                      Manufacturing and testing in SCIF-compliant environments
                    </p>
                  </div>
                </div>
                <div className="flex items-start space-x-4">
                  <div className="flex items-center justify-center w-8 h-8 bg-white/20 rounded-full flex-shrink-0 mt-1">
                    <FileText className="h-4 w-4" />
                  </div>
                  <div>
                    <h4 className="font-semibold mb-2">Documentation</h4>
                    <p className="text-primary-100 text-sm">
                      Complete documentation packages for audit and compliance
                    </p>
                  </div>
                </div>
              </div>
            </div>
            
            <div className="bg-white/10 p-8 rounded-lg">
              <h3 className="text-2xl font-bold mb-6">Government-Only Policy</h3>
              <div className="space-y-4 text-primary-100">
                <p className="flex items-center space-x-3">
                  <CheckCircle className="h-5 w-5 text-green-400 flex-shrink-0" />
                  <span>Exclusively serves US Government agencies</span>
                </p>
                <p className="flex items-center space-x-3">
                  <CheckCircle className="h-5 w-5 text-green-400 flex-shrink-0" />
                  <span>No commercial or private sector clients</span>
                </p>
                <p className="flex items-center space-x-3">
                  <CheckCircle className="h-5 w-5 text-green-400 flex-shrink-0" />
                  <span>Dedicated government support teams</span>
                </p>
                <p className="flex items-center space-x-3">
                  <CheckCircle className="h-5 w-5 text-green-400 flex-shrink-0" />
                  <span>Specialized government pricing</span>
                </p>
                <p className="flex items-center space-x-3">
                  <CheckCircle className="h-5 w-5 text-green-400 flex-shrink-0" />
                  <span>Priority access to resources</span>
                </p>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* CTA Section */}
      <section className="py-20 bg-secondary-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <h2 className="text-3xl md:text-4xl font-bold text-secondary-900 mb-6">
            Ready to Partner with PUBUILD?
          </h2>
          <p className="text-xl text-secondary-600 mb-8 max-w-2xl mx-auto">
            Join the federal agencies that trust us for their critical server infrastructure needs.
          </p>
          <div className="flex flex-col sm:flex-row gap-4 justify-center">
            <Link
              href="/contact"
              className="inline-flex items-center px-8 py-3 bg-primary-600 text-white font-semibold rounded-lg hover:bg-primary-700 transition-colors"
            >
              Start Your Project
              <ArrowRight className="ml-2 h-5 w-5" />
            </Link>
            <Link
              href="/bidding"
              className="inline-flex items-center px-8 py-3 border-2 border-primary-600 text-primary-600 font-semibold rounded-lg hover:bg-primary-600 hover:text-white transition-colors"
            >
              View Government Bids
            </Link>
          </div>
        </div>
      </section>
    </div>
  )
}