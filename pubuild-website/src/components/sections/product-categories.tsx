import Link from 'next/link'
import { Button } from '@/components/ui/button'
import { Server, Monitor, Cpu, HardDrive, Network, Headphones } from 'lucide-react'

const categories = [
  {
    name: 'Servers',
    description: 'Enterprise servers for data centers and government facilities',
    href: '/products/servers',
    icon: Server,
    features: ['Rackmount & Tower', 'Intel & AMD', 'High Availability']
  },
  {
    name: 'Rackmount',
    description: 'Professional rackmount equipment and infrastructure',
    href: '/products/rackmount',
    icon: Server,
    features: ['1U to 4U', 'Network Equipment', 'Power Distribution']
  },
  {
    name: 'Workstations',
    description: 'High-performance workstations for professionals',
    href: '/products/workstations',
    icon: Monitor,
    features: ['AI/ML Ready', 'CAD Workstations', 'Custom Builds']
  },
  {
    name: 'GPUs',
    description: 'Graphics processing units for AI and HPC workloads',
    href: '/products/gpus',
    icon: Cpu,
    features: ['NVIDIA RTX', 'Data Center GPUs', 'AI Training']
  },
  {
    name: 'Storage',
    description: 'Enterprise storage solutions and data management',
    href: '/products/storage',
    icon: HardDrive,
    features: ['SSD & HDD', 'RAID Arrays', 'Network Storage']
  },
  {
    name: 'Networking',
    description: 'Network infrastructure and connectivity solutions',
    href: '/products/networking',
    icon: Network,
    features: ['Switches & Routers', 'Firewalls', 'Wireless']
  },
  {
    name: 'Monitors',
    description: 'Professional displays and monitor solutions',
    href: '/products/monitors',
    icon: Monitor,
    features: ['4K & 8K', 'Color Accurate', 'Multi-Monitor']
  },
  {
    name: 'Accessories',
    description: 'Cables, adapters, and essential IT accessories',
    href: '/products/accessories',
    icon: Headphones,
    features: ['Cables & Adapters', 'Mounts & Stands', 'Tools']
  }
]

export default function ProductCategories() {
  return (
    <section className="py-20 bg-gray-50">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="text-center mb-16">
          <h2 className="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
            Our Product Categories
          </h2>
          <p className="text-xl text-gray-600 max-w-3xl mx-auto">
            Comprehensive IT hardware solutions for government agencies, 
            enterprises, and professional organizations.
          </p>
        </div>

        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
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
                </div>
                
                <h3 className="text-xl font-semibold text-gray-900 mb-2">
                  {category.name}
                </h3>
                
                <p className="text-gray-600 mb-4 text-sm">
                  {category.description}
                </p>

                <ul className="space-y-1 mb-6">
                  {category.features.map((feature, index) => (
                    <li key={index} className="text-sm text-gray-500 flex items-center">
                      <div className="w-1.5 h-1.5 bg-primary rounded-full mr-2"></div>
                      {feature}
                    </li>
                  ))}
                </ul>

                <Button asChild variant="outline" className="w-full group-hover:bg-primary group-hover:text-white transition-colors duration-300">
                  <Link href={category.href}>
                    View Products
                  </Link>
                </Button>
              </div>
            )
          })}
        </div>

        <div className="text-center mt-12">
          <Button asChild size="lg">
            <Link href="/products">
              View All Products
            </Link>
          </Button>
        </div>
      </div>
    </section>
  )
}