import Layout from '@/components/layout/layout'
import { Server, Monitor, Cpu, HardDrive, Network, Headphones } from 'lucide-react'
import Link from 'next/link'
import { Button } from '@/components/ui/button'

const categories = [
  {
    name: 'Servers',
    description: 'Enterprise servers for data centers and government facilities. High-performance, reliable, and scalable solutions.',
    href: '/products/servers',
    icon: Server,
    features: ['Rackmount & Tower Servers', 'Intel & AMD Processors', 'High Availability Configurations', 'Government-Grade Security'],
    specs: '1U to 4U form factors, up to 2TB RAM, multiple drive bays'
  },
  {
    name: 'Rackmount',
    description: 'Professional rackmount equipment and infrastructure for data centers and server rooms.',
    href: '/products/rackmount',
    icon: Server,
    features: ['1U to 4U Chassis', 'Network Equipment', 'Power Distribution Units', 'Cable Management'],
    specs: 'Standard 19" rack compatibility, hot-swappable components'
  },
  {
    name: 'Workstations',
    description: 'High-performance workstations for professionals, engineers, and government agencies.',
    href: '/products/workstations',
    icon: Monitor,
    features: ['AI/ML Ready', 'CAD Workstations', 'Custom Builds', 'Professional GPUs'],
    specs: 'Intel/AMD processors, professional graphics, ECC memory support'
  },
  {
    name: 'GPUs',
    description: 'Graphics processing units for AI, HPC, and professional visualization workloads.',
    href: '/products/gpus',
    icon: Cpu,
    features: ['NVIDIA RTX Series', 'Data Center GPUs', 'AI Training Cards', 'Professional Visualization'],
    specs: 'CUDA cores, Tensor cores, high memory bandwidth'
  },
  {
    name: 'Storage',
    description: 'Enterprise storage solutions and data management for mission-critical applications.',
    href: '/products/storage',
    icon: HardDrive,
    features: ['SSD & HDD Arrays', 'RAID Controllers', 'Network Storage', 'Backup Solutions'],
    specs: 'NVMe SSDs, SAS/SATA drives, hardware RAID support'
  },
  {
    name: 'Networking',
    description: 'Network infrastructure and connectivity solutions for enterprise environments.',
    href: '/products/networking',
    icon: Network,
    features: ['Switches & Routers', 'Firewalls', 'Wireless Access Points', 'Fiber Optic'],
    specs: 'Gigabit/10GbE/25GbE, PoE+, managed switches'
  },
  {
    name: 'Monitors',
    description: 'Professional displays and monitor solutions for government and enterprise use.',
    href: '/products/monitors',
    icon: Monitor,
    features: ['4K & 8K Displays', 'Color Accurate', 'Multi-Monitor Setups', 'Ergonomic Mounts'],
    specs: 'IPS panels, wide color gamut, multiple input options'
  },
  {
    name: 'Accessories',
    description: 'Cables, adapters, and essential IT accessories for complete solutions.',
    href: '/products/accessories',
    icon: Headphones,
    features: ['Cables & Adapters', 'Mounts & Stands', 'Tools & Kits', 'Power Supplies'],
    specs: 'High-quality connectors, professional grade materials'
  }
]

export default function ProductsPage() {
  return (
    <Layout>
      <div className="bg-gray-50 py-16">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-12">
            <h1 className="text-4xl md:text-5xl font-bold text-gray-900 mb-4">
              IT Hardware Products
            </h1>
            <p className="text-xl text-gray-600 max-w-3xl mx-auto">
              Professional IT hardware solutions for government agencies, 
              enterprises, and mission-critical applications. All products 
              meet federal security and compliance standards.
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            {categories.map((category) => {
              const Icon = category.icon
              return (
                <div
                  key={category.name}
                  className="bg-white rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300 p-6 group"
                >
                  <div className="flex items-center mb-4">
                    <div className="p-3 bg-primary/10 rounded-lg group-hover:bg-primary/20 transition-colors duration-300">
                      <Icon className="h-8 w-8 text-primary" />
                    </div>
                    <h3 className="text-xl font-semibold text-gray-900 ml-3">
                      {category.name}
                    </h3>
                  </div>
                  
                  <p className="text-gray-600 mb-4">
                    {category.description}
                  </p>

                  <div className="mb-4">
                    <h4 className="text-sm font-medium text-gray-700 mb-2">Key Features:</h4>
                    <ul className="space-y-1">
                      {category.features.map((feature, index) => (
                        <li key={index} className="text-sm text-gray-600 flex items-center">
                          <div className="w-1.5 h-1.5 bg-primary rounded-full mr-2"></div>
                          {feature}
                        </li>
                      ))}
                    </ul>
                  </div>

                  <div className="mb-6">
                    <h4 className="text-sm font-medium text-gray-700 mb-1">Specifications:</h4>
                    <p className="text-sm text-gray-600">{category.specs}</p>
                  </div>

                  <Button asChild className="w-full group-hover:bg-primary group-hover:text-white transition-colors duration-300">
                    <Link href={category.href}>
                      View {category.name}
                    </Link>
                  </Button>
                </div>
              )
            })}
          </div>

          <div className="mt-16 bg-primary text-white rounded-2xl p-8 lg:p-12">
            <div className="text-center">
              <h2 className="text-3xl font-bold mb-4">
                Need Custom Hardware Solutions?
              </h2>
              <p className="text-xl text-blue-100 mb-8 max-w-3xl mx-auto">
                Our team can design and configure custom hardware solutions 
                to meet your specific requirements and compliance needs.
              </p>
              <div className="flex flex-col sm:flex-row gap-4 justify-center">
                <Button asChild size="lg" className="bg-white text-primary hover:bg-gray-100">
                  <Link href="/contact">
                    Request Custom Quote
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
      </div>
    </Layout>
  )
}