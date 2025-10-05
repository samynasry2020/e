import Link from 'next/link'
import Image from 'next/image'
import { Phone, Mail, MapPin } from 'lucide-react'

const footerNavigation = {
  products: [
    { name: 'Servers', href: '/products/servers' },
    { name: 'Rackmount', href: '/products/rackmount' },
    { name: 'Workstations', href: '/products/workstations' },
    { name: 'GPUs', href: '/products/gpus' },
    { name: 'Storage', href: '/products/storage' },
    { name: 'Networking', href: '/products/networking' },
    { name: 'Monitors', href: '/products/monitors' },
  ],
  solutions: [
    { name: 'AI/HPC Infrastructure', href: '/solutions/ai-hpc' },
    { name: 'Data Center Builds', href: '/solutions/data-center' },
    { name: 'Workstation Fleets', href: '/solutions/workstation-fleets' },
  ],
  company: [
    { name: 'About Us', href: '/about' },
    { name: 'Government Contracting', href: '/government' },
    { name: 'Resources', href: '/resources' },
    { name: 'Contact', href: '/contact' },
  ],
  legal: [
    { name: 'Privacy Policy', href: '/privacy' },
    { name: 'Terms of Use', href: '/terms' },
    { name: 'Accessibility Statement', href: '/accessibility' },
    { name: 'Cookie Policy', href: '/cookies' },
  ],
}

export default function Footer() {
  return (
    <footer className="bg-gray-900 text-white" role="contentinfo">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
          {/* Company Info */}
          <div className="lg:col-span-1">
            <Link href="/" className="flex items-center mb-4">
              <Image
                src="/logo.svg"
                alt="Pubuild Logo"
                width={200}
                height={60}
                className="h-12 w-auto filter brightness-0 invert"
              />
            </Link>
            <p className="text-gray-300 text-sm mb-4">
              Professional IT hardware solutions and government contracting services. 
              Your trusted partner for servers, workstations, and enterprise infrastructure.
            </p>
            <div className="space-y-2 text-sm">
              <div className="flex items-center space-x-2">
                <Phone className="h-4 w-4" />
                <span>800-474-1388</span>
              </div>
              <div className="flex items-center space-x-2">
                <Mail className="h-4 w-4" />
                <span>sales@pubuild.com</span>
              </div>
              <div className="flex items-center space-x-2">
                <MapPin className="h-4 w-4" />
                <span>California, USA</span>
              </div>
            </div>
          </div>

          {/* Products */}
          <div>
            <h3 className="text-lg font-semibold mb-4">Products</h3>
            <ul className="space-y-2">
              {footerNavigation.products.map((item) => (
                <li key={item.name}>
                  <Link
                    href={item.href}
                    className="text-gray-300 hover:text-white text-sm transition-colors duration-200"
                  >
                    {item.name}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          {/* Solutions */}
          <div>
            <h3 className="text-lg font-semibold mb-4">Solutions</h3>
            <ul className="space-y-2">
              {footerNavigation.solutions.map((item) => (
                <li key={item.name}>
                  <Link
                    href={item.href}
                    className="text-gray-300 hover:text-white text-sm transition-colors duration-200"
                  >
                    {item.name}
                  </Link>
                </li>
              ))}
            </ul>
          </div>

          {/* Company & Legal */}
          <div>
            <h3 className="text-lg font-semibold mb-4">Company</h3>
            <ul className="space-y-2 mb-6">
              {footerNavigation.company.map((item) => (
                <li key={item.name}>
                  <Link
                    href={item.href}
                    className="text-gray-300 hover:text-white text-sm transition-colors duration-200"
                  >
                    {item.name}
                  </Link>
                </li>
              ))}
            </ul>
            
            <h4 className="text-md font-semibold mb-4">Legal</h4>
            <ul className="space-y-2">
              {footerNavigation.legal.map((item) => (
                <li key={item.name}>
                  <Link
                    href={item.href}
                    className="text-gray-300 hover:text-white text-sm transition-colors duration-200"
                  >
                    {item.name}
                  </Link>
                </li>
              ))}
            </ul>
          </div>
        </div>

        {/* Bottom section */}
        <div className="border-t border-gray-800 mt-8 pt-8">
          <div className="flex flex-col md:flex-row justify-between items-center">
            <div className="text-sm text-gray-400 mb-4 md:mb-0">
              <p>&copy; {new Date().getFullYear()} Pubuild. All rights reserved.</p>
              <p className="mt-1">
                We are an independent supplier. No government endorsement implied.
              </p>
            </div>
            <div className="flex items-center space-x-4 text-sm text-gray-400">
              <span>NAICS: 334111, 423430, 541512</span>
              <span>•</span>
              <span>Export controlled products</span>
            </div>
          </div>
        </div>
      </div>
    </footer>
  )
}