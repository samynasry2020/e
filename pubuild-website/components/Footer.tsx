import Link from 'next/link';
import Image from 'next/image';
import { COMPANY } from '@/lib/constants';

export default function Footer() {
  const currentYear = new Date().getFullYear();

  return (
    <footer className="bg-gray-900 text-white" role="contentinfo">
      <div className="container py-12">
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">
          {/* Company Info */}
          <div>
            <Image
              src="/logo.svg"
              alt="PU Build Logo"
              width={180}
              height={54}
              className="h-12 w-auto mb-4 brightness-0 invert"
            />
            <p className="text-gray-400 text-sm mb-4">
              Enterprise IT solutions for government and commercial organizations. Servers, workstations, storage, and networking infrastructure.
            </p>
            <div className="space-y-2 text-sm">
              <div>
                <a
                  href={`tel:${COMPANY.phoneRaw}`}
                  className="text-gray-300 hover:text-white transition-colors"
                  aria-label={`Call ${COMPANY.phone}`}
                >
                  📞 {COMPANY.phone}
                </a>
              </div>
              <div>
                <a
                  href={`mailto:${COMPANY.email}`}
                  className="text-gray-300 hover:text-white transition-colors"
                >
                  ✉️ {COMPANY.email}
                </a>
              </div>
              <div className="text-gray-400">
                {COMPANY.hours.weekdays}
              </div>
            </div>
          </div>

          {/* Products */}
          <div>
            <h3 className="font-bold text-lg mb-4">Products</h3>
            <ul className="space-y-2 text-sm">
              <li>
                <Link href="/products/servers" className="text-gray-400 hover:text-white transition-colors">
                  Servers
                </Link>
              </li>
              <li>
                <Link href="/products/workstations" className="text-gray-400 hover:text-white transition-colors">
                  Workstations
                </Link>
              </li>
              <li>
                <Link href="/products/gpus" className="text-gray-400 hover:text-white transition-colors">
                  GPUs & Accelerators
                </Link>
              </li>
              <li>
                <Link href="/products/storage" className="text-gray-400 hover:text-white transition-colors">
                  Storage Solutions
                </Link>
              </li>
              <li>
                <Link href="/products/networking" className="text-gray-400 hover:text-white transition-colors">
                  Networking
                </Link>
              </li>
              <li>
                <Link href="/products/rackmount" className="text-gray-400 hover:text-white transition-colors">
                  Rackmount Systems
                </Link>
              </li>
              <li>
                <Link href="/products/monitors" className="text-gray-400 hover:text-white transition-colors">
                  Monitors & Displays
                </Link>
              </li>
              <li>
                <Link href="/products/accessories" className="text-gray-400 hover:text-white transition-colors">
                  Accessories
                </Link>
              </li>
            </ul>
          </div>

          {/* Solutions & Company */}
          <div>
            <h3 className="font-bold text-lg mb-4">Solutions</h3>
            <ul className="space-y-2 text-sm mb-6">
              <li>
                <Link href="/solutions/ai-hpc-infrastructure" className="text-gray-400 hover:text-white transition-colors">
                  AI & HPC Infrastructure
                </Link>
              </li>
              <li>
                <Link href="/solutions/data-center-builds" className="text-gray-400 hover:text-white transition-colors">
                  Data Center Builds
                </Link>
              </li>
              <li>
                <Link href="/solutions/workstation-fleets" className="text-gray-400 hover:text-white transition-colors">
                  Workstation Fleets
                </Link>
              </li>
            </ul>

            <h3 className="font-bold text-lg mb-4">Company</h3>
            <ul className="space-y-2 text-sm">
              <li>
                <Link href="/government" className="text-gray-400 hover:text-white transition-colors">
                  Government Contracting
                </Link>
              </li>
              <li>
                <Link href="/about" className="text-gray-400 hover:text-white transition-colors">
                  About Us
                </Link>
              </li>
              <li>
                <Link href="/resources" className="text-gray-400 hover:text-white transition-colors">
                  Resources
                </Link>
              </li>
              <li>
                <Link href="/contact" className="text-gray-400 hover:text-white transition-colors">
                  Contact
                </Link>
              </li>
            </ul>
          </div>

          {/* Legal */}
          <div>
            <h3 className="font-bold text-lg mb-4">Legal & Compliance</h3>
            <ul className="space-y-2 text-sm">
              <li>
                <Link href="/privacy" className="text-gray-400 hover:text-white transition-colors">
                  Privacy Policy
                </Link>
              </li>
              <li>
                <Link href="/terms" className="text-gray-400 hover:text-white transition-colors">
                  Terms of Use
                </Link>
              </li>
              <li>
                <Link href="/accessibility" className="text-gray-400 hover:text-white transition-colors">
                  Accessibility Statement
                </Link>
              </li>
              <li>
                <Link href="/cookies" className="text-gray-400 hover:text-white transition-colors">
                  Cookie Policy
                </Link>
              </li>
            </ul>

            <div className="mt-6 p-4 bg-gray-800 rounded-lg text-xs text-gray-400">
              <p className="font-semibold mb-1">Compliance Note:</p>
              <p>We are an independent supplier. No government endorsement is implied.</p>
            </div>
          </div>
        </div>

        {/* Bottom Bar */}
        <div className="border-t border-gray-800 pt-8 mt-8">
          <div className="flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
            <p className="text-sm text-gray-400">
              © {currentYear} {COMPANY.legalName}. All rights reserved.
            </p>
            <div className="flex items-center space-x-6 text-sm text-gray-400">
              <span>NAICS: 334111, 423430, 541512</span>
              <span>UEI: {COMPANY.uei}</span>
              <span>CAGE: {COMPANY.cage}</span>
            </div>
          </div>
        </div>
      </div>
    </footer>
  );
}
