import { Shield, Lock, Award, CheckCircle, FileText, Users } from 'lucide-react'

export default function SecurityPage() {
  const securityFeatures = [
    {
      icon: Shield,
      title: "FIPS 140-2 Level 4",
      description: "Hardware security modules certified to the highest FIPS standards"
    },
    {
      icon: Lock,
      title: "End-to-End Encryption",
      description: "Military-grade encryption for data at rest and in transit"
    },
    {
      icon: Award,
      title: "FedRAMP High",
      description: "Authorized at the highest level for government cloud services"
    },
    {
      icon: Users,
      title: "Cleared Personnel",
      description: "Team members with appropriate security clearances"
    }
  ]

  const certifications = [
    "FedRAMP High Authorization",
    "FIPS 140-2 Level 4 Certified",
    "Common Criteria EAL4+",
    "ISO 27001:2013",
    "SOC 2 Type II",
    "NIST Cybersecurity Framework",
    "FISMA Compliant",
    "DoD SRG Level 5"
  ]

  return (
    <div className="min-h-screen">
      {/* Hero Section */}
      <section className="bg-gradient-to-r from-primary-600 to-primary-800 text-white py-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center">
            <h1 className="text-4xl md:text-5xl font-bold mb-6">
              Security & Compliance
            </h1>
            <p className="text-xl md:text-2xl mb-8 max-w-4xl mx-auto opacity-90">
              Government-grade security measures and compliance standards 
              protecting our nation's critical infrastructure.
            </p>
          </div>
        </div>
      </section>

      {/* Security Features */}
      <section className="py-20 bg-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-16">
            <h2 className="text-3xl md:text-4xl font-bold text-secondary-900 mb-4">
              Security Features
            </h2>
            <p className="text-xl text-secondary-600 max-w-3xl mx-auto">
              Comprehensive security measures designed for government operations
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            {securityFeatures.map((feature, index) => (
              <div key={index} className="bg-secondary-50 p-6 rounded-lg text-center">
                <div className="flex items-center justify-center w-12 h-12 bg-primary-100 rounded-lg mx-auto mb-4">
                  <feature.icon className="h-6 w-6 text-primary-600" />
                </div>
                <h3 className="text-lg font-semibold text-secondary-900 mb-3">
                  {feature.title}
                </h3>
                <p className="text-secondary-600">
                  {feature.description}
                </p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Certifications */}
      <section className="py-20 bg-secondary-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-16">
            <h2 className="text-3xl md:text-4xl font-bold text-secondary-900 mb-4">
              Security Certifications
            </h2>
            <p className="text-xl text-secondary-600 max-w-3xl mx-auto">
              Maintaining the highest security standards required for government service
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            {certifications.map((cert, index) => (
              <div key={index} className="flex items-center space-x-3 bg-white p-4 rounded-lg shadow-sm">
                <CheckCircle className="h-5 w-5 text-green-500 flex-shrink-0" />
                <span className="text-secondary-700 font-medium">{cert}</span>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Security Practices */}
      <section className="py-20 bg-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-16">
            <h2 className="text-3xl md:text-4xl font-bold text-secondary-900 mb-4">
              Security Practices
            </h2>
          </div>

          <div className="grid grid-cols-1 lg:grid-cols-2 gap-12">
            <div>
              <h3 className="text-2xl font-bold text-secondary-900 mb-6">
                Physical Security
              </h3>
              <ul className="space-y-4 text-secondary-700">
                <li className="flex items-start space-x-3">
                  <CheckCircle className="h-5 w-5 text-green-500 flex-shrink-0 mt-0.5" />
                  <span>SCIF-compliant manufacturing facilities</span>
                </li>
                <li className="flex items-start space-x-3">
                  <CheckCircle className="h-5 w-5 text-green-500 flex-shrink-0 mt-0.5" />
                  <span>24/7 armed security and surveillance</span>
                </li>
                <li className="flex items-start space-x-3">
                  <CheckCircle className="h-5 w-5 text-green-500 flex-shrink-0 mt-0.5" />
                  <span>Biometric access controls</span>
                </li>
                <li className="flex items-start space-x-3">
                  <CheckCircle className="h-5 w-5 text-green-500 flex-shrink-0 mt-0.5" />
                  <span>Secure component storage</span>
                </li>
              </ul>
            </div>

            <div>
              <h3 className="text-2xl font-bold text-secondary-900 mb-6">
                Personnel Security
              </h3>
              <ul className="space-y-4 text-secondary-700">
                <li className="flex items-start space-x-3">
                  <CheckCircle className="h-5 w-5 text-green-500 flex-shrink-0 mt-0.5" />
                  <span>Background investigations for all personnel</span>
                </li>
                <li className="flex items-start space-x-3">
                  <CheckCircle className="h-5 w-5 text-green-500 flex-shrink-0 mt-0.5" />
                  <span>Security clearances up to TS/SCI</span>
                </li>
                <li className="flex items-start space-x-3">
                  <CheckCircle className="h-5 w-5 text-green-500 flex-shrink-0 mt-0.5" />
                  <span>Continuous security training</span>
                </li>
                <li className="flex items-start space-x-3">
                  <CheckCircle className="h-5 w-5 text-green-500 flex-shrink-0 mt-0.5" />
                  <span>Regular polygraph examinations</span>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </section>

      {/* Contact Security Team */}
      <section className="py-20 bg-secondary-900 text-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <h2 className="text-3xl md:text-4xl font-bold mb-6">
            Security Questions?
          </h2>
          <p className="text-xl mb-8 max-w-2xl mx-auto opacity-90">
            Contact our security team for detailed information about our 
            security measures and compliance certifications.
          </p>
          <div className="flex flex-col sm:flex-row gap-4 justify-center">
            <a
              href="mailto:security@pubuild.com"
              className="inline-flex items-center px-8 py-3 bg-primary-600 text-white font-semibold rounded-lg hover:bg-primary-700 transition-colors"
            >
              <FileText className="h-5 w-5 mr-2" />
              Security Inquiries
            </a>
            <a
              href="tel:+15551234570"
              className="inline-flex items-center px-8 py-3 border-2 border-white text-white font-semibold rounded-lg hover:bg-white hover:text-secondary-900 transition-colors"
            >
              <Shield className="h-5 w-5 mr-2" />
              Security Hotline
            </a>
          </div>
        </div>
      </section>
    </div>
  )
}