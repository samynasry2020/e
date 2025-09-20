'use client'

import React, { useState } from 'react'
import { 
  Mail, 
  Phone, 
  MapPin, 
  Clock, 
  Send, 
  Building2,
  Shield,
  Users,
  CheckCircle,
  AlertCircle
} from 'lucide-react'

export default function ContactPage() {
  const [formData, setFormData] = useState({
    firstName: '',
    lastName: '',
    email: '',
    phone: '',
    agency: '',
    clearanceLevel: '',
    projectType: '',
    timeline: '',
    budget: '',
    message: ''
  })

  const [isSubmitting, setIsSubmitting] = useState(false)
  const [submitStatus, setSubmitStatus] = useState<'success' | 'error' | null>(null)

  const handleInputChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>) => {
    setFormData({
      ...formData,
      [e.target.name]: e.target.value
    })
  }

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setIsSubmitting(true)
    
    // Simulate form submission
    setTimeout(() => {
      setIsSubmitting(false)
      setSubmitStatus('success')
      // Reset form
      setFormData({
        firstName: '',
        lastName: '',
        email: '',
        phone: '',
        agency: '',
        clearanceLevel: '',
        projectType: '',
        timeline: '',
        budget: '',
        message: ''
      })
    }, 2000)
  }

  return (
    <div className="min-h-screen">
      {/* Hero Section */}
      <section className="bg-gradient-to-r from-primary-600 to-primary-800 text-white py-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center">
            <h1 className="text-4xl md:text-5xl font-bold mb-6">
              Contact Our Government Team
            </h1>
            <p className="text-xl md:text-2xl mb-8 max-w-4xl mx-auto opacity-90">
              Ready to discuss your government server solution needs? Our specialized 
              team is here to help with contracts, proposals, and technical requirements.
            </p>
            <div className="flex flex-col sm:flex-row gap-4 justify-center">
              <div className="flex items-center space-x-2 bg-white/10 px-4 py-2 rounded-lg">
                <Shield className="h-5 w-5" />
                <span>Security Cleared Team</span>
              </div>
              <div className="flex items-center space-x-2 bg-white/10 px-4 py-2 rounded-lg">
                <Building2 className="h-5 w-5" />
                <span>Government Exclusive</span>
              </div>
              <div className="flex items-center space-x-2 bg-white/10 px-4 py-2 rounded-lg">
                <Clock className="h-5 w-5" />
                <span>24/7 Support</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Contact Form Section */}
      <section className="py-20 bg-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-12">
            {/* Contact Form */}
            <div>
              <h2 className="text-3xl font-bold text-secondary-900 mb-6">
                Government Project Inquiry
              </h2>
              <p className="text-secondary-600 mb-8">
                Complete this form to discuss your government server solution requirements. 
                All inquiries are handled with appropriate security protocols.
              </p>

              {submitStatus === 'success' && (
                <div className="mb-6 p-4 bg-green-50 border border-green-200 rounded-lg">
                  <div className="flex items-center space-x-2">
                    <CheckCircle className="h-5 w-5 text-green-600" />
                    <span className="text-green-800 font-medium">
                      Thank you! Your inquiry has been submitted successfully.
                    </span>
                  </div>
                </div>
              )}

              <form onSubmit={handleSubmit} className="space-y-6">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <label htmlFor="firstName" className="block text-sm font-medium text-secondary-700 mb-2">
                      First Name *
                    </label>
                    <input
                      type="text"
                      id="firstName"
                      name="firstName"
                      value={formData.firstName}
                      onChange={handleInputChange}
                      required
                      className="w-full px-4 py-2 border border-secondary-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                    />
                  </div>
                  <div>
                    <label htmlFor="lastName" className="block text-sm font-medium text-secondary-700 mb-2">
                      Last Name *
                    </label>
                    <input
                      type="text"
                      id="lastName"
                      name="lastName"
                      value={formData.lastName}
                      onChange={handleInputChange}
                      required
                      className="w-full px-4 py-2 border border-secondary-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                    />
                  </div>
                </div>

                <div>
                  <label htmlFor="email" className="block text-sm font-medium text-secondary-700 mb-2">
                    Government Email *
                  </label>
                  <input
                    type="email"
                    id="email"
                    name="email"
                    value={formData.email}
                    onChange={handleInputChange}
                    required
                    className="w-full px-4 py-2 border border-secondary-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                    placeholder="name@agency.gov"
                  />
                </div>

                <div>
                  <label htmlFor="agency" className="block text-sm font-medium text-secondary-700 mb-2">
                    Government Agency *
                  </label>
                  <select
                    id="agency"
                    name="agency"
                    value={formData.agency}
                    onChange={handleInputChange}
                    required
                    className="w-full px-4 py-2 border border-secondary-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                  >
                    <option value="">Select your agency</option>
                    <option value="dod">Department of Defense</option>
                    <option value="dhs">Department of Homeland Security</option>
                    <option value="fbi">Federal Bureau of Investigation</option>
                    <option value="cia">Central Intelligence Agency</option>
                    <option value="nsa">National Security Agency</option>
                    <option value="other">Other Federal Agency</option>
                  </select>
                </div>

                <div>
                  <label htmlFor="message" className="block text-sm font-medium text-secondary-700 mb-2">
                    Project Details *
                  </label>
                  <textarea
                    id="message"
                    name="message"
                    value={formData.message}
                    onChange={handleInputChange}
                    required
                    rows={6}
                    className="w-full px-4 py-2 border border-secondary-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                    placeholder="Please describe your server solution requirements..."
                  ></textarea>
                </div>

                <div className="bg-secondary-50 p-4 rounded-lg">
                  <div className="flex items-start space-x-2">
                    <AlertCircle className="h-5 w-5 text-secondary-500 flex-shrink-0 mt-0.5" />
                    <div className="text-sm text-secondary-600">
                      <p className="font-medium mb-1">Security Notice:</p>
                      <p>
                        Please do not include classified information in this form.
                      </p>
                    </div>
                  </div>
                </div>

                <button
                  type="submit"
                  disabled={isSubmitting}
                  className="w-full flex items-center justify-center px-6 py-3 bg-primary-600 text-white font-semibold rounded-lg hover:bg-primary-700 transition-colors disabled:opacity-50"
                >
                  {isSubmitting ? (
                    <>
                      <div className="animate-spin rounded-full h-5 w-5 border-b-2 border-white mr-2"></div>
                      Submitting...
                    </>
                  ) : (
                    <>
                      <Send className="h-5 w-5 mr-2" />
                      Submit Inquiry
                    </>
                  )}
                </button>
              </form>
            </div>

            {/* Contact Information */}
            <div>
              <h2 className="text-3xl font-bold text-secondary-900 mb-6">
                Contact Information
              </h2>
              
              <div className="space-y-6">
                <div className="flex items-start space-x-4">
                  <div className="flex items-center justify-center w-12 h-12 bg-primary-100 rounded-lg flex-shrink-0">
                    <Mail className="h-6 w-6 text-primary-600" />
                  </div>
                  <div>
                    <h3 className="text-lg font-semibold text-secondary-900 mb-1">
                      Government Contracts
                    </h3>
                    <p className="text-primary-600 font-medium mb-1">
                      contracts@pubuild.com
                    </p>
                    <p className="text-secondary-600 text-sm">
                      Primary contact for government contracting inquiries
                    </p>
                  </div>
                </div>

                <div className="flex items-start space-x-4">
                  <div className="flex items-center justify-center w-12 h-12 bg-primary-100 rounded-lg flex-shrink-0">
                    <Phone className="h-6 w-6 text-primary-600" />
                  </div>
                  <div>
                    <h3 className="text-lg font-semibold text-secondary-900 mb-1">
                      Government Sales
                    </h3>
                    <p className="text-primary-600 font-medium mb-1">
                      +1 (555) 123-4567
                    </p>
                    <p className="text-secondary-600 text-sm">
                      Direct line to our government sales team
                    </p>
                  </div>
                </div>

                <div className="flex items-start space-x-4">
                  <div className="flex items-center justify-center w-12 h-12 bg-primary-100 rounded-lg flex-shrink-0">
                    <MapPin className="h-6 w-6 text-primary-600" />
                  </div>
                  <div>
                    <h3 className="text-lg font-semibold text-secondary-900 mb-1">
                      Corporate Headquarters
                    </h3>
                    <p className="text-primary-600 font-medium mb-1">
                      Washington, DC Metropolitan Area
                    </p>
                    <p className="text-secondary-600 text-sm">
                      Strategically located near federal agencies
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Emergency Contact */}
      <section className="py-12 bg-red-50 border-t-4 border-red-500">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center">
            <h2 className="text-2xl font-bold text-red-900 mb-4">
              Emergency Government Support
            </h2>
            <p className="text-red-700 mb-6">
              For critical system failures or emergency government support needs
            </p>
            <div className="flex flex-col sm:flex-row gap-4 justify-center">
              <a
                href="tel:+15551234571"
                className="inline-flex items-center px-6 py-3 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-colors"
              >
                <Phone className="h-5 w-5 mr-2" />
                Emergency: +1 (555) 123-4571
              </a>
              <a
                href="mailto:emergency@pubuild.com"
                className="inline-flex items-center px-6 py-3 border-2 border-red-600 text-red-600 font-semibold rounded-lg hover:bg-red-600 hover:text-white transition-colors"
              >
                <Mail className="h-5 w-5 mr-2" />
                emergency@pubuild.com
              </a>
            </div>
          </div>
        </div>
      </section>
    </div>
  )
}