import Layout from '@/components/layout/layout'
import { Shield, Zap, HardDrive, Cpu } from 'lucide-react'
import Link from 'next/link'
import { Button } from '@/components/ui/button'

const serverTypes = [
  {
    name: 'Rackmount Servers',
    description: '1U to 4U rackmount servers for data center deployments',
    features: ['1U to 4U Form Factors', 'Dual/Quad Socket Support', 'Hot-Swappable Drives', 'Redundant Power Supplies'],
    applications: ['Web Hosting', 'Database Servers', 'Virtualization', 'Government Applications']
  },
  {
    name: 'Tower Servers',
    description: 'Standalone tower servers for office and small data center environments',
    features: ['Compact Design', 'Quiet Operation', 'Easy Maintenance', 'Expandable Storage'],
    applications: ['File Servers', 'Print Servers', 'Small Business', 'Remote Offices']
  },
  {
    name: 'Blade Servers',
    description: 'High-density blade servers for maximum computing power per rack',
    features: ['High Density', 'Shared Infrastructure', 'Modular Design', 'Efficient Cooling'],
    applications: ['HPC Clusters', 'Cloud Computing', 'Large Scale Virtualization', 'AI/ML Workloads']
  }
]

const specifications = [
  { category: 'Processors', details: 'Intel Xeon, AMD EPYC, up to 64 cores' },
  { category: 'Memory', details: 'Up to 2TB DDR4/DDR5 ECC memory' },
  { category: 'Storage', details: 'NVMe SSDs, SAS/SATA drives, hardware RAID' },
  { category: 'Networking', details: 'Gigabit/10GbE/25GbE Ethernet, InfiniBand' },
  { category: 'Power', details: '80+ Platinum efficiency, redundant PSUs' },
  { category: 'Management', details: 'IPMI, iDRAC, iLO remote management' }
]

export default function ServersPage() {
  return (
    <Layout>
      <div className="bg-white">
        {/* Hero Section */}
        <div className="bg-gradient-to-r from-primary to-secondary text-white py-20">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="text-center">
              <h1 className="text-4xl md:text-5xl font-bold mb-4">
                Enterprise Servers
              </h1>
              <p className="text-xl text-blue-100 max-w-3xl mx-auto">
                High-performance servers designed for government agencies, 
                enterprises, and mission-critical applications. Built for 
                reliability, security, and compliance.
              </p>
            </div>
          </div>
        </div>

        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
          {/* Server Types */}
          <div className="mb-16">
            <h2 className="text-3xl font-bold text-gray-900 mb-8 text-center">
              Server Types & Configurations
            </h2>
            <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
              {serverTypes.map((type, index) => (
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
              Why Choose Our Servers
            </h2>
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
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
                  <Zap className="h-8 w-8 text-primary" />
                </div>
                <h3 className="text-lg font-semibold text-gray-900 mb-2">Performance</h3>
                <p className="text-gray-600 text-sm">
                  High-performance processors and memory for demanding workloads
                </p>
              </div>
              
              <div className="text-center">
                <div className="bg-primary/10 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                  <HardDrive className="h-8 w-8 text-primary" />
                </div>
                <h3 className="text-lg font-semibold text-gray-900 mb-2">Storage</h3>
                <p className="text-gray-600 text-sm">
                  Flexible storage options with hardware RAID and hot-swappable drives
                </p>
              </div>
              
              <div className="text-center">
                <div className="bg-primary/10 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                  <Cpu className="h-8 w-8 text-primary" />
                </div>
                <h3 className="text-lg font-semibold text-gray-900 mb-2">Reliability</h3>
                <p className="text-gray-600 text-sm">
                  Enterprise-grade components with redundant power and cooling
                </p>
              </div>
            </div>
          </div>

          {/* CTA Section */}
          <div className="bg-primary text-white rounded-2xl p-8 lg:p-12 text-center">
            <h2 className="text-3xl font-bold mb-4">
              Ready to Deploy Your Server Infrastructure?
            </h2>
            <p className="text-xl text-blue-100 mb-8 max-w-3xl mx-auto">
              Contact our team to discuss your server requirements and get a 
              custom configuration that meets your specific needs.
            </p>
            <div className="flex flex-col sm:flex-row gap-4 justify-center">
              <Button asChild size="lg" className="bg-white text-primary hover:bg-gray-100">
                <Link href="/contact">
                  Get Server Quote
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