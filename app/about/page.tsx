import Link from 'next/link'
import { 
  Users, 
  Award, 
  Shield, 
  Building2,
  Target,
  Zap,
  CheckCircle,
  ArrowRight,
  Globe,
  Clock,
  Star
} from 'lucide-react'

export default function AboutPage() {
  const stats = [
    { number: "15+", label: "Years of Experience" },
    { number: "100+", label: "Government Projects" },
    { number: "50+", label: "Federal Agencies Served" },
    { number: "99.9%", label: "System Uptime" },
    { number: "$50M+", label: "Contract Value Delivered" },
    { number: "24/7", label: "Government Support" }
  ]

  const values = [
    {
      icon: Shield,
      title: "Security First",
      description: "Every solution is built with government-grade security as the foundation, ensuring the highest levels of protection for sensitive operations."
    },
    {
      icon: Award,
      title: "Excellence in Service",
      description: "We maintain the highest standards of quality and reliability, delivering solutions that exceed government expectations consistently."
    },
    {
      icon: Users,
      title: "Government Focused",
      description: "Our exclusive focus on government clients allows us to develop deep expertise in federal requirements and compliance standards."
    },
    {
      icon: Zap,
      title: "Rapid Response",
      description: "Understanding the critical nature of government operations, we provide rapid deployment and emergency support capabilities."
    }
  ]

  const leadership = [
    {
      name: "Robert Johnson",
      title: "Chief Executive Officer",
      background: "Former DoD contractor with 20+ years in government technology solutions",
      clearance: "TS/SCI"
    },
    {
      name: "Sarah Mitchell",
      title: "Chief Technology Officer",
      background: "Ex-NSA systems architect specializing in secure server infrastructure",
      clearance: "TS/SCI"
    },
    {
      name: "Michael Chen",
      title: "VP of Government Sales",
      background: "15 years in federal contracting and government relationship management",
      clearance: "Secret"
    },
    {
      name: "Jennifer Davis",
      title: "Director of Security",
      background: "Former DHS cybersecurity specialist with expertise in federal compliance",
      clearance: "Top Secret"
    }
  ]

  const certifications = [
    "FedRAMP High Authorization",
    "FIPS 140-2 Level 4 Certified",
    "Common Criteria EAL4+",
    "ISO 27001:2013 Certified",
    "SOC 2 Type II Compliant",
    "NIST Cybersecurity Framework",
    "GSA Schedule Contract Holder",
    "SEWP VI Contract Vehicle",
    "CIO-SP3 Prime Contractor",
    "OASIS Contract Holder"
  ]

  const timeline = [
    {
      year: "2010",
      title: "Company Founded",
      description: "PUBUILD TECHNOLOGIES INC. established with exclusive focus on government server solutions"
    },
    {
      year: "2012",
      title: "First Major Contract",
      description: "Awarded $5M contract for DoD secure server infrastructure deployment"
    },
    {
      year: "2015",
      title: "Security Certifications",
      description: "Achieved FedRAMP High authorization and FIPS 140-2 Level 4 certification"
    },
    {
      year: "2018",
      title: "Intelligence Community",
      description: "Expanded services to intelligence agencies with TS/SCI cleared team"
    },
    {
      year: "2021",
      title: "COVID Response",
      description: "Rapid deployment of remote work infrastructure for government agencies"
    },
    {
      year: "2025",
      title: "Continued Excellence",
      description: "Serving 50+ federal agencies with cutting-edge server solutions"
    }
  ]

  return (
    <div className="min-h-screen">
      {/* Hero Section */}
      <section className="bg-gradient-to-r from-secondary-900 to-secondary-800 text-white py-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center">
            <h1 className="text-4xl md:text-5xl font-bold mb-6">
              About PUBUILD TECHNOLOGIES INC.
            </h1>
            <p className="text-xl md:text-2xl mb-8 max-w-4xl mx-auto opacity-90">
              For over 15 years, we have been the trusted partner for US Government 
              agencies seeking reliable, secure, and compliant server solutions.
            </p>
            <div className="flex flex-col sm:flex-row gap-4 justify-center">
              <div className="flex items-center space-x-2 bg-white/10 px-4 py-2 rounded-lg">
                <Building2 className="h-5 w-5" />
                <span>Government Exclusive Since 2010</span>
              </div>
              <div className="flex items-center space-x-2 bg-white/10 px-4 py-2 rounded-lg">
                <Shield className="h-5 w-5" />
                <span>TS/SCI Cleared Team</span>
              </div>
              <div className="flex items-center space-x-2 bg-white/10 px-4 py-2 rounded-lg">
                <Award className="h-5 w-5" />
                <span>Multiple Certifications</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Stats Section */}
      <section className="py-16 bg-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-8">
            {stats.map((stat, index) => (
              <div key={index} className="text-center">
                <div className="text-3xl md:text-4xl font-bold text-primary-600 mb-2">
                  {stat.number}
                </div>
                <div className="text-secondary-600 font-medium text-sm">
                  {stat.label}
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Mission Statement */}
      <section className="py-20 bg-secondary-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-16">
            <h2 className="text-3xl md:text-4xl font-bold text-secondary-900 mb-6">
              Our Mission
            </h2>
            <p className="text-xl text-secondary-600 max-w-4xl mx-auto leading-relaxed">
              To provide the United States Government with the most secure, reliable, 
              and advanced server solutions available, ensuring our nation's critical 
              infrastructure operates at peak performance while maintaining the highest 
              levels of security and compliance.
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            {values.map((value, index) => (
              <div key={index} className="bg-white p-6 rounded-lg shadow-md text-center">
                <div className="flex items-center justify-center w-12 h-12 bg-primary-100 rounded-lg mx-auto mb-4">
                  <value.icon className="h-6 w-6 text-primary-600" />
                </div>
                <h3 className="text-xl font-semibold text-secondary-900 mb-3">
                  {value.title}
                </h3>
                <p className="text-secondary-600">
                  {value.description}
                </p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Leadership Team */}
      <section className="py-20 bg-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-16">
            <h2 className="text-3xl md:text-4xl font-bold text-secondary-900 mb-4">
              Leadership Team
            </h2>
            <p className="text-xl text-secondary-600 max-w-3xl mx-auto">
              Our leadership team combines decades of government service with 
              deep technical expertise and security clearances.
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
            {leadership.map((leader, index) => (
              <div key={index} className="bg-secondary-50 p-6 rounded-lg">
                <div className="flex items-start space-x-4">
                  <div className="flex items-center justify-center w-16 h-16 bg-primary-600 text-white rounded-full font-bold text-xl flex-shrink-0">
                    {leader.name.split(' ').map(n => n[0]).join('')}
                  </div>
                  <div className="flex-1">
                    <h3 className="text-xl font-bold text-secondary-900 mb-1">
                      {leader.name}
                    </h3>
                    <p className="text-primary-600 font-semibold mb-3">
                      {leader.title}
                    </p>
                    <p className="text-secondary-600 mb-3 text-sm">
                      {leader.background}
                    </p>
                    <div className="inline-block bg-primary-100 text-primary-800 px-3 py-1 rounded-full text-xs font-medium">
                      {leader.clearance} Clearance
                    </div>
                  </div>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Company Timeline */}
      <section className="py-20 bg-secondary-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-16">
            <h2 className="text-3xl md:text-4xl font-bold text-secondary-900 mb-4">
              Our Journey
            </h2>
            <p className="text-xl text-secondary-600 max-w-3xl mx-auto">
              15 years of dedicated service to the US Government
            </p>
          </div>

          <div className="relative">
            <div className="absolute left-1/2 transform -translate-x-1/2 w-1 h-full bg-primary-200"></div>
            <div className="space-y-12">
              {timeline.map((item, index) => (
                <div key={index} className={`flex items-center ${index % 2 === 0 ? 'justify-start' : 'justify-end'}`}>
                  <div className={`w-full max-w-md ${index % 2 === 0 ? 'pr-8 text-right' : 'pl-8 text-left'}`}>
                    <div className="bg-white p-6 rounded-lg shadow-md relative">
                      <div className={`absolute top-6 ${index % 2 === 0 ? '-right-3' : '-left-3'} w-6 h-6 bg-primary-600 rounded-full border-4 border-white`}></div>
                      <div className="text-2xl font-bold text-primary-600 mb-2">
                        {item.year}
                      </div>
                      <h3 className="text-lg font-semibold text-secondary-900 mb-2">
                        {item.title}
                      </h3>
                      <p className="text-secondary-600">
                        {item.description}
                      </p>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          </div>
        </div>
      </section>

      {/* Certifications */}
      <section className="py-20 bg-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-16">
            <h2 className="text-3xl md:text-4xl font-bold text-secondary-900 mb-4">
              Certifications & Compliance
            </h2>
            <p className="text-xl text-secondary-600 max-w-3xl mx-auto">
              We maintain the highest levels of certification and compliance 
              required for government contracting.
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            {certifications.map((cert, index) => (
              <div key={index} className="flex items-center space-x-3 bg-secondary-50 p-4 rounded-lg">
                <CheckCircle className="h-5 w-5 text-green-500 flex-shrink-0" />
                <span className="text-secondary-700 font-medium">{cert}</span>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Government Exclusive Policy */}
      <section className="py-20 bg-primary-600 text-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center">
            <h2 className="text-3xl md:text-4xl font-bold mb-6">
              Government Exclusive Policy
            </h2>
            <p className="text-xl mb-8 max-w-3xl mx-auto opacity-90">
              Unlike other technology companies, PUBUILD TECHNOLOGIES INC. works 
              exclusively with the US Government. We do not serve commercial clients, 
              ensuring our full focus remains on government requirements.
            </p>
            
            <div className="grid grid-cols-1 md:grid-cols-3 gap-8 mt-12">
              <div className="bg-white/10 p-6 rounded-lg">
                <Building2 className="h-8 w-8 mx-auto mb-4" />
                <h3 className="text-lg font-semibold mb-2">100% Government Focus</h3>
                <p className="text-primary-100">
                  No commercial distractions - all resources dedicated to government success
                </p>
              </div>
              <div className="bg-white/10 p-6 rounded-lg">
                <Shield className="h-8 w-8 mx-auto mb-4" />
                <h3 className="text-lg font-semibold mb-2">Security Specialized</h3>
                <p className="text-primary-100">
                  Deep expertise in government security requirements and compliance
                </p>
              </div>
              <div className="bg-white/10 p-6 rounded-lg">
                <Users className="h-8 w-8 mx-auto mb-4" />
                <h3 className="text-lg font-semibold mb-2">Cleared Personnel</h3>
                <p className="text-primary-100">
                  Team members with appropriate security clearances for classified work
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
            Partner with PUBUILD TECHNOLOGIES INC.
          </h2>
          <p className="text-xl text-secondary-600 mb-8 max-w-2xl mx-auto">
            Join the 50+ federal agencies that trust us for their critical 
            server infrastructure needs.
          </p>
          <div className="flex flex-col sm:flex-row gap-4 justify-center">
            <Link
              href="/contact"
              className="inline-flex items-center px-8 py-3 bg-primary-600 text-white font-semibold rounded-lg hover:bg-primary-700 transition-colors"
            >
              Contact Our Team
              <ArrowRight className="ml-2 h-5 w-5" />
            </Link>
            <Link
              href="/government"
              className="inline-flex items-center px-8 py-3 border-2 border-primary-600 text-primary-600 font-semibold rounded-lg hover:bg-primary-600 hover:text-white transition-colors"
            >
              Government Solutions
            </Link>
          </div>
        </div>
      </section>
    </div>
  )
}