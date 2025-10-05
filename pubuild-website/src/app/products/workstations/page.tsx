import Layout from '@/components/layout/layout'
import { Monitor, Cpu, Zap, Shield, HardDrive, Network } from 'lucide-react'
import Link from 'next/link'
import { Button } from '@/components/ui/button'

const workstationTypes = [
  {
    name: 'AI/ML Workstations',
    description: 'High-performance workstations optimized for artificial intelligence and machine learning workloads',
    features: ['NVIDIA RTX/Quadro GPUs', 'High-Core CPUs', 'Large Memory Capacity', 'Fast Storage'],
    applications: ['Deep Learning', 'Data Science', 'Computer Vision', 'Model Training']
  },
  {
    name: 'CAD Workstations',
    description: 'Professional workstations for computer-aided design and engineering applications',
    features: ['Professional GPUs', 'Precision Displays', 'Certified Software', 'Reliable Performance'],
    applications: ['AutoCAD', 'SolidWorks', 'Revit', 'Engineering Design']
  },
  {
    name: 'Government Workstations',
    description: 'Secure workstations designed for government agencies and sensitive environments',
    features: ['Security Hardening', 'Compliance Ready', 'Trusted Platform Module', 'Encrypted Storage'],
    applications: ['Government Agencies', 'Defense Contractors', 'Sensitive Data', 'Secure Computing']
  }
]

const specifications = [
  { category: 'Processors', details: 'Intel Core i7/i9, Xeon, AMD Ryzen/Ryzen Pro' },
  { category: 'Graphics', details: 'NVIDIA RTX, Quadro, AMD Radeon Pro, Intel Arc' },
  { category: 'Memory', details: 'Up to 128GB DDR4/DDR5 ECC memory' },
  { category: 'Storage', details: 'NVMe SSDs, SATA drives, RAID configurations' },
  { category: 'Displays', details: '4K/8K monitors, color-accurate panels' },
  { category: 'Connectivity', details: 'USB-C, Thunderbolt, Ethernet, Wi-Fi 6E' }
]

export default function WorkstationsPage() {
  return (
    <Layout>
      <div className="bg-white">
        {/* Hero Section */}
        <div className="bg-gradient-to-r from-primary to-secondary text-white py-20">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="text-center">
              <h1 className="text-4xl md:text-5xl font-bold mb-4">
                Professional Workstations
              </h1>
              <p className="text-xl text-blue-100 max-w-3xl mx-auto">
                High-performance workstations for professionals, engineers, 
                and government agencies. Built for demanding applications 
                and mission-critical work.
              </p>
            </div>
          </div>
        </div>

        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
          {/* Workstation Types */}
          <div className="mb-16">
            <h2 className="text-3xl font-bold text-gray-900 mb-8 text-center">
              Workstation Categories
            </h2>
            <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
              {workstationTypes.map((type, index) => (
                <div key={index} className="bg-gray-50 rounded-lg p-6">
                  <h3 className="text-xl font-semibold text-gray-900 mb-3">
                    {type.name}
                  </h3>
                  <p className="text-gray-600 mb-4">
                    {type.description}
                  </p>
                  
                  <div className="mb-4">
                    <h4 className="font-medium text-gray-700 mb-2">Key Features:</h4>
                    <ul className="space-y-1">
                      {type.features.map((feature, idx) => (
                        <li key={idx} className="text-sm text-gray-600 flex items-center">
                          <div className="w-1.5 h-1.5 bg-primary rounded-full mr-2"></div>
                          {feature}
                        </li>
                      ))}
                    </ul>
                  </div>

                  <div>
                    <h4 className="font-medium text-gray-700 mb-2">Applications:</h4>
                    <ul className="space-y-1">
                      {type.applications.map((app, idx) => (
                        <li key={idx} className="text-sm text-gray-600 flex items-center">
                          <div className="w-1.5 h-1.5 bg-secondary rounded-full mr-2"></div>
                          {app}
                        </li>
                      ))}
                    </ul>
                  </div>
                </div>
              ))}
            </div>
          </div>

          {/* Specifications */}
          <div className="mb-16">
            <h2 className="text-3xl font-bold text-gray-900 mb-8 text-center">
              Technical Specifications
            </h2>
            <div className="bg-gray-50 rounded-lg p-8">
              <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                {specifications.map((spec, index) => (
                  <div key={index} className="bg-white rounded-lg p-4">
                    <h3 className="font-semibold text-gray-900 mb-2">{spec.category}</h3>
                    <p className="text-gray-600 text-sm">{spec.details}</p>
                  </div>
                ))}
              </div>
            </div>
          </div>

          {/* Features & Benefits */}
          <div className="mb-16">
            <h2 className="text-3xl font-bold text-gray-900 mb-8 text-center">
              Why Choose Our Workstations
            </h2>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
              <div className="text-center">
                <div className="bg-primary/10 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                  <Cpu className="h-8 w-8 text-primary" />
                </div>
                <h3 className="text-lg font-semibold text-gray-900 mb-2">Performance</h3>
                <p className="text-gray-600 text-sm">
                  Latest processors and graphics cards for maximum performance
                </p>
              </div>
              
              <div className="text-center">
                <div className="bg-primary/10 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                  <Shield className="h-8 w-8 text-primary" />
                </div>
                <h3 className="text-lg font-semibold text-gray-900 mb-2">Security</h3>
                <p className="text-gray-600 text-sm">
                  Built-in security features and compliance with government standards
                </p>
              </div>
              
              <div className="text-center">
                <div className="bg-primary/10 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                  <Monitor className="h-8 w-8 text-primary" />
                </div>
                <h3 className="text-lg font-semibold text-gray-900 mb-2">Displays</h3>
                <p className="text-gray-600 text-sm">
                  High-resolution, color-accurate monitors for professional work
                </p>
              </div>
              
              <div className="text-center">
                <div className="bg-primary/10 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                  <Zap className="h-8 w-8 text-primary" />
                </div>
                <h3 className="text-lg font-semibold text-gray-900 mb-2">Reliability</h3>
                <p className="text-gray-600 text-sm">
                  Enterprise-grade components with extended warranties
                </p>
              </div>
              
              <div className="text-center">
                <div className="bg-primary/10 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                  <HardDrive className="h-8 w-8 text-primary" />
                </div>
                <h3 className="text-lg font-semibold text-gray-900 mb-2">Storage</h3>
                <p className="text-gray-600 text-sm">
                  Fast NVMe SSDs and large capacity storage options
                </p>
              </div>
              
              <div className="text-center">
                <div className="bg-primary/10 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                  <Network className="h-8 w-8 text-primary" />
                </div>
                <h3 className="text-lg font-semibold text-gray-900 mb-2">Connectivity</h3>
                <p className="text-gray-600 text-sm">
                  Latest connectivity options including Thunderbolt and USB-C
                </p>
              </div>
            </div>
          </div>

          {/* CTA Section */}
          <div className="bg-primary text-white rounded-2xl p-8 lg:p-12 text-center">
            <h2 className="text-3xl font-bold mb-4">
              Ready to Upgrade Your Workstations?
            </h2>
            <p className="text-xl text-blue-100 mb-8 max-w-3xl mx-auto">
              Contact our team to discuss your workstation requirements and get 
              a custom configuration that meets your specific needs.
            </p>
            <div className="flex flex-col sm:flex-row gap-4 justify-center">
              <Button asChild size="lg" className="bg-white text-primary hover:bg-gray-100">
                <Link href="/contact">
                  Get Workstation Quote
                </Link>
              </Button>
              <Button asChild size="lg" variant="outline" className="border-white text-white hover:bg-white hover:text-primary">
                <Link href="/government">
                  Government Contracting
                </Link>
              </Button>
            </div>
          </div>
        </div>
      </div>
    </Layout>
  )
}