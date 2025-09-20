import Link from 'next/link'
import { Server, Shield, Mail, Phone, MapPin } from 'lucide-react'

export default function Footer() {
  return (
    <footer className="bg-secondary-900 text-white">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
          {/* Company Info */}
          <div className="col-span-1 md:col-span-2">
            <div className="flex items-center space-x-2 mb-4">
              <div className="flex items-center space-x-1">
                <Server className="h-8 w-8 text-primary-400" />
                <Shield className="h-6 w-6 text-secondary-400" />
              </div>
              <div className="flex flex-col">
                <span className="text-xl font-bold text-primary-400">PUBUILD</span>
                <span className="text-xs text-secondary-300 -mt-1">TECHNOLOGIES INC.</span>
              </div>
            </div>
            <p className="text-secondary-300 mb-4 max-w-md">
              Specialized in server building, setup, manufacturing, and design services 
              exclusively for the US Government and federal agencies. Your trusted partner 
              for government technology solutions.
            </p>
            <div className="space-y-2 text-sm text-secondary-300">
              <div className="flex items-center space-x-2">
                <Mail className="h-4 w-4" />
                <span>contracts@pubuild.com</span>
              </div>
              <div className="flex items-center space-x-2">
                <Phone className="h-4 w-4" />
                <span>+1 (555) 123-4567</span>
              </div>
              <div className="flex items-center space-x-2">
                <MapPin className="h-4 w-4" />
                <span>Washington, DC Metropolitan Area</span>
              </div>
            </div>
          </div>

          {/* Quick Links */}
          <div>
            <h3 className="text-lg font-semibold mb-4">Quick Links</h3>
            <ul className="space-y-2 text-secondary-300">
              <li>
                <Link href="/services" className="hover:text-primary-400 transition-colors">
                  Our Services
                </Link>
              </li>
              <li>
                <Link href="/government" className="hover:text-primary-400 transition-colors">
                  Government Solutions
                </Link>
              </li>
              <li>
                <Link href="/bidding" className="hover:text-primary-400 transition-colors">
                  Bidding Portal
                </Link>
              </li>
              <li>
                <Link href="/about" className="hover:text-primary-400 transition-colors">
                  About Us
                </Link>
              </li>
            </ul>
          </div>

          {/* Government Services */}
          <div>
            <h3 className="text-lg font-semibold mb-4">Government Focus</h3>
            <ul className="space-y-2 text-secondary-300 text-sm">
              <li>Federal Agencies</li>
              <li>Defense Contracts</li>
              <li>Secure Server Solutions</li>
              <li>Government Bidding</li>
              <li>Compliance & Certifications</li>
            </ul>
          </div>
        </div>

        <div className="border-t border-secondary-700 mt-8 pt-8">
          <div className="flex flex-col md:flex-row justify-between items-center">
            <div className="text-secondary-400 text-sm">
              © 2025 PUBUILD TECHNOLOGIES INC. All rights reserved.
            </div>
            <div className="flex space-x-6 mt-4 md:mt-0">
              <Link href="/privacy" className="text-secondary-400 hover:text-primary-400 text-sm transition-colors">
                Privacy Policy
              </Link>
              <Link href="/terms" className="text-secondary-400 hover:text-primary-400 text-sm transition-colors">
                Terms of Service
              </Link>
              <Link href="/security" className="text-secondary-400 hover:text-primary-400 text-sm transition-colors">
                Security
              </Link>
            </div>
          </div>
        </div>
      </div>
    </footer>
  )
}