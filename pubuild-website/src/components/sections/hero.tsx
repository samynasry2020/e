import Link from 'next/link'
import { Button } from '@/components/ui/button'
import { ArrowRight, Shield, Zap, Users } from 'lucide-react'

export default function Hero() {
  return (
    <section className="bg-gradient-to-br from-primary to-secondary text-white py-20">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
          <div>
            <h1 className="text-4xl md:text-5xl lg:text-6xl font-bold leading-tight mb-6">
              Professional IT Hardware & Government Solutions
            </h1>
            <p className="text-xl text-blue-100 mb-8 leading-relaxed">
              Your trusted partner for servers, workstations, rackmount equipment, 
              and government contracting services. Delivering enterprise-grade 
              infrastructure solutions with unmatched reliability.
            </p>
            
            <div className="flex flex-col sm:flex-row gap-4 mb-8">
              <Button size="lg" asChild className="bg-white text-primary hover:bg-gray-100">
                <Link href="/contact">
                  Get Free Quote
                  <ArrowRight className="ml-2 h-5 w-5" />
                </Link>
              </Button>
              <Button size="lg" variant="outline" asChild className="border-white text-white hover:bg-white hover:text-primary">
                <Link href="/government">
                  Government Contracting
                </Link>
              </Button>
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-3 gap-6">
              <div className="flex items-center space-x-3">
                <Shield className="h-8 w-8 text-blue-200" />
                <div>
                  <div className="font-semibold">Secure & Compliant</div>
                  <div className="text-sm text-blue-200">Government standards</div>
                </div>
              </div>
              <div className="flex items-center space-x-3">
                <Zap className="h-8 w-8 text-blue-200" />
                <div>
                  <div className="font-semibold">Fast Delivery</div>
                  <div className="text-sm text-blue-200">Quick turnaround</div>
                </div>
              </div>
              <div className="flex items-center space-x-3">
                <Users className="h-8 w-8 text-blue-200" />
                <div>
                  <div className="font-semibold">Expert Support</div>
                  <div className="text-sm text-blue-200">24/7 assistance</div>
                </div>
              </div>
            </div>
          </div>

          <div className="relative">
            <div className="bg-white/10 backdrop-blur-sm rounded-2xl p-8 border border-white/20">
              <h3 className="text-2xl font-bold mb-6">Why Choose Pubuild?</h3>
              <ul className="space-y-4">
                <li className="flex items-start space-x-3">
                  <div className="w-2 h-2 bg-blue-200 rounded-full mt-2 flex-shrink-0"></div>
                  <div>
                    <div className="font-semibold">Government Contracting Expertise</div>
                    <div className="text-blue-200 text-sm">NAICS certified, experienced in federal procurement</div>
                  </div>
                </li>
                <li className="flex items-start space-x-3">
                  <div className="w-2 h-2 bg-blue-200 rounded-full mt-2 flex-shrink-0"></div>
                  <div>
                    <div className="font-semibold">Enterprise-Grade Hardware</div>
                    <div className="text-blue-200 text-sm">Servers, workstations, GPUs, networking equipment</div>
                  </div>
                </li>
                <li className="flex items-start space-x-3">
                  <div className="w-2 h-2 bg-blue-200 rounded-full mt-2 flex-shrink-0"></div>
                  <div>
                    <div className="font-semibold">Custom Solutions</div>
                    <div className="text-blue-200 text-sm">Tailored infrastructure for your specific needs</div>
                  </div>
                </li>
                <li className="flex items-start space-x-3">
                  <div className="w-2 h-2 bg-blue-200 rounded-full mt-2 flex-shrink-0"></div>
                  <div>
                    <div className="font-semibold">Compliance Ready</div>
                    <div className="text-blue-200 text-sm">Export controls, security standards, certifications</div>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
      </div>
    </section>
  )
}