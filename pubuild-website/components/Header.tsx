'use client';

import Link from 'next/link';
import Image from 'next/image';
import { useState, useEffect } from 'react';
import { COMPANY } from '@/lib/constants';

export default function Header() {
  const [isScrolled, setIsScrolled] = useState(false);
  const [isMobileMenuOpen, setIsMobileMenuOpen] = useState(false);

  useEffect(() => {
    const handleScroll = () => {
      setIsScrolled(window.scrollY > 20);
    };
    window.addEventListener('scroll', handleScroll);
    return () => window.removeEventListener('scroll', handleScroll);
  }, []);

  const productCategories = [
    { name: 'Servers', href: '/products/servers' },
    { name: 'Workstations', href: '/products/workstations' },
    { name: 'GPUs & Accelerators', href: '/products/gpus' },
    { name: 'Storage', href: '/products/storage' },
    { name: 'Networking', href: '/products/networking' },
    { name: 'Rackmount', href: '/products/rackmount' },
    { name: 'Monitors', href: '/products/monitors' },
    { name: 'Accessories', href: '/products/accessories' },
  ];

  const solutions = [
    { name: 'AI & HPC Infrastructure', href: '/solutions/ai-hpc-infrastructure' },
    { name: 'Data Center Builds', href: '/solutions/data-center-builds' },
    { name: 'Workstation Fleets', href: '/solutions/workstation-fleets' },
  ];

  return (
    <>
      <a href="#main-content" className="skip-link">
        Skip to main content
      </a>
      
      <header
        className={`fixed top-0 left-0 right-0 z-50 transition-all duration-300 ${
          isScrolled ? 'bg-white shadow-md' : 'bg-white/95 backdrop-blur-sm'
        }`}
        role="banner"
      >
        <div className="container">
          <nav className="flex items-center justify-between py-4" aria-label="Main navigation">
            {/* Logo */}
            <Link href="/" className="flex items-center space-x-2" aria-label="PU Build Home">
              <Image
                src="/logo.svg"
                alt="PU Build Logo"
                width={180}
                height={54}
                priority
                className="h-12 w-auto"
              />
            </Link>

            {/* Desktop Navigation */}
            <div className="hidden lg:flex items-center space-x-8">
              {/* Products Dropdown */}
              <div className="relative group">
                <button
                  className="text-gray-700 hover:text-primary font-semibold transition-colors flex items-center"
                  aria-haspopup="true"
                  aria-expanded="false"
                >
                  Products
                  <svg className="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
                  </svg>
                </button>
                <div className="absolute top-full left-0 mt-2 w-64 bg-white shadow-xl rounded-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 border border-gray-200">
                  <div className="py-2">
                    {productCategories.map((category) => (
                      <Link
                        key={category.href}
                        href={category.href}
                        className="block px-4 py-2 text-gray-700 hover:bg-gray-50 hover:text-primary transition-colors"
                      >
                        {category.name}
                      </Link>
                    ))}
                  </div>
                </div>
              </div>

              {/* Solutions Dropdown */}
              <div className="relative group">
                <button
                  className="text-gray-700 hover:text-primary font-semibold transition-colors flex items-center"
                  aria-haspopup="true"
                  aria-expanded="false"
                >
                  Solutions
                  <svg className="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
                  </svg>
                </button>
                <div className="absolute top-full left-0 mt-2 w-64 bg-white shadow-xl rounded-lg opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 border border-gray-200">
                  <div className="py-2">
                    {solutions.map((solution) => (
                      <Link
                        key={solution.href}
                        href={solution.href}
                        className="block px-4 py-2 text-gray-700 hover:bg-gray-50 hover:text-primary transition-colors"
                      >
                        {solution.name}
                      </Link>
                    ))}
                  </div>
                </div>
              </div>

              <Link href="/government" className="text-gray-700 hover:text-primary font-semibold transition-colors">
                Government
              </Link>

              <Link href="/about" className="text-gray-700 hover:text-primary font-semibold transition-colors">
                About
              </Link>

              <Link href="/resources" className="text-gray-700 hover:text-primary font-semibold transition-colors">
                Resources
              </Link>

              <Link href="/contact" className="btn btn-primary">
                Contact Us
              </Link>

              <a
                href={`tel:${COMPANY.phoneRaw}`}
                className="text-primary font-bold hover:text-primary-dark transition-colors"
                aria-label={`Call us at ${COMPANY.phone}`}
              >
                {COMPANY.phone}
              </a>
            </div>

            {/* Mobile Menu Button */}
            <button
              className="lg:hidden p-2 text-gray-700 hover:text-primary transition-colors"
              onClick={() => setIsMobileMenuOpen(!isMobileMenuOpen)}
              aria-label="Toggle mobile menu"
              aria-expanded={isMobileMenuOpen}
            >
              {isMobileMenuOpen ? (
                <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                </svg>
              ) : (
                <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 12h16M4 18h16" />
                </svg>
              )}
            </button>
          </nav>

          {/* Mobile Menu */}
          {isMobileMenuOpen && (
            <div className="lg:hidden py-4 border-t border-gray-200">
              <div className="space-y-4">
                <div>
                  <div className="font-semibold text-gray-900 mb-2">Products</div>
                  <div className="pl-4 space-y-2">
                    {productCategories.map((category) => (
                      <Link
                        key={category.href}
                        href={category.href}
                        className="block text-gray-700 hover:text-primary transition-colors py-1"
                        onClick={() => setIsMobileMenuOpen(false)}
                      >
                        {category.name}
                      </Link>
                    ))}
                  </div>
                </div>

                <div>
                  <div className="font-semibold text-gray-900 mb-2">Solutions</div>
                  <div className="pl-4 space-y-2">
                    {solutions.map((solution) => (
                      <Link
                        key={solution.href}
                        href={solution.href}
                        className="block text-gray-700 hover:text-primary transition-colors py-1"
                        onClick={() => setIsMobileMenuOpen(false)}
                      >
                        {solution.name}
                      </Link>
                    ))}
                  </div>
                </div>

                <Link
                  href="/government"
                  className="block text-gray-700 hover:text-primary font-semibold transition-colors py-2"
                  onClick={() => setIsMobileMenuOpen(false)}
                >
                  Government Contracting
                </Link>

                <Link
                  href="/about"
                  className="block text-gray-700 hover:text-primary font-semibold transition-colors py-2"
                  onClick={() => setIsMobileMenuOpen(false)}
                >
                  About
                </Link>

                <Link
                  href="/resources"
                  className="block text-gray-700 hover:text-primary font-semibold transition-colors py-2"
                  onClick={() => setIsMobileMenuOpen(false)}
                >
                  Resources
                </Link>

                <Link
                  href="/contact"
                  className="block btn btn-primary w-full text-center"
                  onClick={() => setIsMobileMenuOpen(false)}
                >
                  Contact Us
                </Link>

                <a
                  href={`tel:${COMPANY.phoneRaw}`}
                  className="block text-center text-primary font-bold hover:text-primary-dark transition-colors py-2 border-2 border-primary rounded-lg"
                >
                  {COMPANY.phone}
                </a>
              </div>
            </div>
          )}
        </div>
      </header>

      {/* Spacer to prevent content from going under fixed header */}
      <div className="h-20" aria-hidden="true"></div>
    </>
  );
}
