import Link from 'next/link'
import { ArrowRight, Server, Shield, Users, Award, CheckCircle, Building2 } from 'lucide-react'

export default function Home() {
  const services = [
    {
      icon: Server,
      title: "Server Manufacturing",
      description: "Custom-built servers designed to meet stringent government specifications and security requirements."
    },
    {
      icon: Shield,
      title: "Secure Setup & Configuration",
      description: "Professional server setup and configuration with government-grade security protocols."
    },
    {
      icon: Building2,
      title: "Enterprise Solutions",
      description: "Scalable server solutions for large-scale government operations and federal agencies."
    },
    {
      icon: Award,
      title: "Compliance & Certification",
      description: "All solutions meet federal compliance standards and security certifications."
    }
  ]

  const stats = [
    { number: "15+", label: "Years of Experience" },
    { number: "100+", label: "Government Projects" },
    { number: "50+", label: "Federal Agencies Served" },
    { number: "99.9%", label: "Uptime Guarantee" }
  ]

  const benefits = [
    "Exclusive focus on government sector",
    "Security clearance certified team",
    "Federal compliance guaranteed",
    "24/7 government support",
    "Competitive bidding expertise",
    "Rapid deployment capabilities"
  ]

  return (
    <div className="min-h-screen">
      {/* Hero Section */}
      <section className="hero-gradient text-white py-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center">
            <h1 className="text-4xl md:text-6xl font-bold mb-6">
              Government Server Solutions
              <span className="block text-2xl md:text-4xl font-normal mt-2 opacity-90">
                Built for Federal Excellence
              </span>
            </h1>
            <p className="text-xl md:text-2xl mb-8 max-w-3xl mx-auto opacity-90">
              PUBUILD TECHNOLOGIES INC. specializes in server building, manufacturing, 
              and design exclusively for US Government agencies and federal contracts.
            </p>
            <div className="flex flex-col sm:flex-row gap-4 justify-center">
              <Link
                href="/services"
                className="inline-flex items-center px-8 py-3 bg-white text-primary-600 font-semibold rounded-lg hover:bg-secondary-100 transition-colors"
              >
                Explore Our Services
                <ArrowRight className="ml-2 h-5 w-5" />
              </Link>
              <Link
                href="/bidding"
                className="inline-flex items-center px-8 py-3 border-2 border-white text-white font-semibold rounded-lg hover:bg-white hover:text-primary-600 transition-colors"
              >
                Government Bidding Portal
              </Link>
            </div>
          </div>
        </div>
      </section>

      {/* Stats Section */}
      <section className="py-16 bg-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-2 md:grid-cols-4 gap-8">
            {stats.map((stat, index) => (
              <div key={index} className="text-center">
                <div className="text-3xl md:text-4xl font-bold text-primary-600 mb-2">
                  {stat.number}
                </div>
                <div className="text-secondary-600 font-medium">
                  {stat.label}
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Services Overview */}
      <section className="py-20 bg-secondary-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-16">
            <h2 className="text-3xl md:text-4xl font-bold text-secondary-900 mb-4">
              Specialized Government Services
            </h2>
            <p className="text-xl text-secondary-600 max-w-3xl mx-auto">
              We provide comprehensive server solutions tailored specifically for 
              government agencies and federal contracts.
            </p>
          </div>
          
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            {services.map((service, index) => (
              <div key={index} className="bg-white p-6 rounded-lg shadow-md card-hover">
                <div className="flex items-center justify-center w-12 h-12 bg-primary-100 rounded-lg mb-4">
                  <service.icon className="h-6 w-6 text-primary-600" />
                </div>
                <h3 className="text-xl font-semibold text-secondary-900 mb-3">
                  {service.title}
                </h3>
                <p className="text-secondary-600">
                  {service.description}
                </p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Government Focus */}
      <section className="py-20 bg-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            <div>
              <h2 className="text-3xl md:text-4xl font-bold text-secondary-900 mb-6">
                Exclusively Serving Government Agencies
              </h2>
              <p className="text-lg text-secondary-600 mb-8">
                PUBUILD TECHNOLOGIES INC. is dedicated solely to providing server solutions 
                for the US Government. We do not work with private individuals or commercial 
                entities - our focus is 100% on federal agencies and government contracts.
              </p>
              
              <div className="space-y-4">
                {benefits.map((benefit, index) => (
                  <div key={index} className="flex items-center space-x-3">
                    <CheckCircle className="h-5 w-5 text-green-500 flex-shrink-0" />
                    <span className="text-secondary-700">{benefit}</span>
                  </div>
                ))}
              </div>
              
              <div className="mt-8">
                <Link
                  href="/government"
                  className="inline-flex items-center px-6 py-3 bg-primary-600 text-white font-semibold rounded-lg hover:bg-primary-700 transition-colors"
                >
                  Learn More About Our Government Focus
                  <ArrowRight className="ml-2 h-5 w-5" />
                </Link>
              </div>
            </div>
            
            <div className="bg-secondary-900 text-white p-8 rounded-lg">
              <h3 className="text-2xl font-bold mb-6">Government Agencies We Serve</h3>
              <div className="grid grid-cols-1 gap-4">
                <div className="flex items-center space-x-3">
                  <Users className="h-5 w-5 text-primary-400" />
                  <span>Department of Defense</span>
                </div>
                <div className="flex items-center space-x-3">
                  <Users className="h-5 w-5 text-primary-400" />
                  <span>Department of Homeland Security</span>
                </div>
                <div className="flex items-center space-x-3">
                  <Users className="h-5 w-5 text-primary-400" />
                  <span>Federal Bureau of Investigation</span>
                </div>
                <div className="flex items-center space-x-3">
                  <Users className="h-5 w-5 text-primary-400" />
                  <span>Central Intelligence Agency</span>
                </div>
                <div className="flex items-center space-x-3">
                  <Users className="h-5 w-5 text-primary-400" />
                  <span>National Security Agency</span>
                </div>
                <div className="flex items-center space-x-3">
                  <Users className="h-5 w-5 text-primary-400" />
                  <span>And Many More Federal Agencies</span>
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
            Ready to Partner with Us?
          </h2>
          <p className="text-xl mb-8 max-w-2xl mx-auto opacity-90">
            Join the federal agencies that trust PUBUILD TECHNOLOGIES INC. 
            for their critical server infrastructure needs.
          </p>
          <div className="flex flex-col sm:flex-row gap-4 justify-center">
            <Link
              href="/contact"
              className="inline-flex items-center px-8 py-3 bg-white text-primary-600 font-semibold rounded-lg hover:bg-secondary-100 transition-colors"
            >
              Get Started Today
              <ArrowRight className="ml-2 h-5 w-5" />
            </Link>
            <Link
              href="/bidding"
              className="inline-flex items-center px-8 py-3 border-2 border-white text-white font-semibold rounded-lg hover:bg-white hover:text-primary-600 transition-colors"
            >
              View Current Bids
            </Link>
          </div>
        </div>
      </section>
    </div>
  )
}