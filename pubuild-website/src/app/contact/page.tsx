'use client'

import { useState } from 'react'
import Link from 'next/link'
import Layout from '@/components/layout/layout'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import { Textarea } from '@/components/ui/textarea'
import { Label } from '@/components/ui/label'
import { Phone, Mail, MapPin, Clock, CheckCircle } from 'lucide-react'

export default function ContactPage() {
  const [formData, setFormData] = useState({
    name: '',
    email: '',
    phone: '',
    company: '',
    subject: '',
    message: '',
    rfpType: 'general'
  })

  const [isSubmitting, setIsSubmitting] = useState(false)
  const [isSubmitted, setIsSubmitted] = useState(false)

  const handleSubmit = async (e: React.FormEvent) => {
    e.preventDefault()
    setIsSubmitting(true)
    
    // Simulate form submission
    await new Promise(resolve => setTimeout(resolve, 1000))
    
    setIsSubmitting(false)
    setIsSubmitted(true)
  }

  const handleChange = (e: React.ChangeEvent<HTMLInputElement | HTMLTextAreaElement | HTMLSelectElement>) => {
    setFormData({
      ...formData,
      [e.target.name]: e.target.value
    })
  }

  if (isSubmitted) {
    return (
      <Layout>
        <div className="bg-white py-20">
          <div className="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <CheckCircle className="h-16 w-16 text-green-500 mx-auto mb-6" />
            <h1 className="text-3xl font-bold text-gray-900 mb-4">
              Thank You for Your Message
            </h1>
            <p className="text-xl text-gray-600 mb-8">
              We've received your message and will respond within 24 hours. 
              Our team is reviewing your request and will contact you soon.
            </p>
            <Button asChild>
              <Link href="/">Return to Home</Link>
            </Button>
          </div>
        </div>
      </Layout>
    )
  }

  return (
    <Layout>
      <div className="bg-white">
        {/* Hero Section */}
        <div className="bg-gradient-to-r from-primary to-secondary text-white py-20">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="text-center">
              <h1 className="text-4xl md:text-5xl font-bold mb-4">
                Contact Us
              </h1>
              <p className="text-xl text-blue-100 max-w-3xl mx-auto">
                Ready to discuss your IT hardware requirements? Our team is here 
                to help with government contracting, custom solutions, and support.
              </p>
            </div>
          </div>
        </div>

        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-12">
            {/* Contact Form */}
            <div>
              <h2 className="text-2xl font-bold text-gray-900 mb-6">
                Send Us a Message
              </h2>
              <form onSubmit={handleSubmit} className="space-y-6">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <Label htmlFor="name">Name *</Label>
                    <Input
                      id="name"
                      name="name"
                      type="text"
                      required
                      value={formData.name}
                      onChange={handleChange}
                      className="mt-1"
                    />
                  </div>
                  <div>
                    <Label htmlFor="email">Email *</Label>
                    <Input
                      id="email"
                      name="email"
                      type="email"
                      required
                      value={formData.email}
                      onChange={handleChange}
                      className="mt-1"
                    />
                  </div>
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <Label htmlFor="phone">Phone</Label>
                    <Input
                      id="phone"
                      name="phone"
                      type="tel"
                      value={formData.phone}
                      onChange={handleChange}
                      className="mt-1"
                    />
                  </div>
                  <div>
                    <Label htmlFor="company">Company</Label>
                    <Input
                      id="company"
                      name="company"
                      type="text"
                      value={formData.company}
                      onChange={handleChange}
                      className="mt-1"
                    />
                  </div>
                </div>

                <div>
                  <Label htmlFor="subject">Subject *</Label>
                  <Input
                    id="subject"
                    name="subject"
                    type="text"
                    required
                    value={formData.subject}
                    onChange={handleChange}
                    className="mt-1"
                  />
                </div>

                <div>
                  <Label htmlFor="rfpType">Inquiry Type</Label>
                  <select
                    id="rfpType"
                    name="rfpType"
                    value={formData.rfpType}
                    onChange={handleChange}
                    className="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-primary focus:border-primary"
                  >
                    <option value="general">General Inquiry</option>
                    <option value="government">Government Contracting</option>
                    <option value="rfp">RFP Support</option>
                    <option value="custom">Custom Solution</option>
                    <option value="support">Technical Support</option>
                  </select>
                </div>

                <div>
                  <Label htmlFor="message">Message *</Label>
                  <Textarea
                    id="message"
                    name="message"
                    rows={6}
                    required
                    value={formData.message}
                    onChange={handleChange}
                    className="mt-1"
                    placeholder="Please describe your requirements, timeline, and any specific needs..."
                  />
                </div>

                <div className="flex items-start">
                  <input
                    id="consent"
                    name="consent"
                    type="checkbox"
                    required
                    className="mt-1 h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded"
                  />
                  <label htmlFor="consent" className="ml-2 text-sm text-gray-600">
                    I agree to the <a href="/privacy" className="text-primary hover:underline">Privacy Policy</a> and 
                    consent to being contacted about my inquiry.
                  </label>
                </div>

                <Button
                  type="submit"
                  disabled={isSubmitting}
                  className="w-full bg-primary hover:bg-primary/90"
                >
                  {isSubmitting ? 'Sending...' : 'Send Message'}
                </Button>
              </form>
            </div>

            {/* Contact Information */}
            <div>
              <h2 className="text-2xl font-bold text-gray-900 mb-6">
                Get in Touch
              </h2>
              
              <div className="space-y-6 mb-8">
                <div className="flex items-start">
                  <Phone className="h-6 w-6 text-primary mr-4 mt-1" />
                  <div>
                    <h3 className="font-semibold text-gray-900">Phone</h3>
                    <p className="text-gray-600">800-474-1388</p>
                    <p className="text-sm text-gray-500">Mon-Fri 8AM-6PM PST</p>
                  </div>
                </div>

                <div className="flex items-start">
                  <Mail className="h-6 w-6 text-primary mr-4 mt-1" />
                  <div>
                    <h3 className="font-semibold text-gray-900">Email</h3>
                    <p className="text-gray-600">sales@pubuild.com</p>
                    <p className="text-sm text-gray-500">Response within 24 hours</p>
                  </div>
                </div>

                <div className="flex items-start">
                  <MapPin className="h-6 w-6 text-primary mr-4 mt-1" />
                  <div>
                    <h3 className="font-semibold text-gray-900">Location</h3>
                    <p className="text-gray-600">California, USA</p>
                    <p className="text-sm text-gray-500">Serving nationwide</p>
                  </div>
                </div>

                <div className="flex items-start">
                  <Clock className="h-6 w-6 text-primary mr-4 mt-1" />
                  <div>
                    <h3 className="font-semibold text-gray-900">Business Hours</h3>
                    <p className="text-gray-600">Monday - Friday: 8:00 AM - 6:00 PM PST</p>
                    <p className="text-sm text-gray-500">Emergency support available</p>
                  </div>
                </div>
              </div>

              <div className="bg-gray-50 rounded-lg p-6">
                <h3 className="text-lg font-semibold text-gray-900 mb-4">
                  Government Contracting
                </h3>
                <p className="text-gray-600 mb-4">
                  Specialized support for government agencies and contractors. 
                  We understand federal procurement requirements and compliance standards.
                </p>
                <ul className="space-y-2 text-sm text-gray-600">
                  <li>• RFP response and proposal support</li>
                  <li>• NAICS code compliance</li>
                  <li>• Security clearance requirements</li>
                  <li>• Export control compliance</li>
                </ul>
                <Button asChild className="mt-4 w-full">
                  <a href="/government">Learn More</a>
                </Button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </Layout>
  )
}