import Link from 'next/link';
import { generatePageMetadata } from '@/lib/metadata';
import { COMPANY } from '@/lib/constants';

export const metadata = generatePageMetadata({
  title: COMPANY.name,
  description: 'Enterprise IT hardware solutions for government and commercial organizations. Servers, workstations, GPUs, storage, networking, and complete data center infrastructure.',
  path: '/',
});

export default function HomePage() {
  const productCategories = [
    {
      name: 'Servers',
      slug: 'servers',
      icon: '🖥️',
      description: 'Enterprise-grade rackmount servers for mission-critical workloads',
    },
    {
      name: 'Workstations',
      slug: 'workstations',
      icon: '💻',
      description: 'Professional workstations for CAD, engineering, and content creation',
    },
    {
      name: 'GPUs & Accelerators',
      slug: 'gpus',
      icon: '🎮',
      description: 'High-performance GPUs for AI, machine learning, and HPC',
    },
    {
      name: 'Storage',
      slug: 'storage',
      icon: '💾',
      description: 'Enterprise storage solutions including NAS, SAN, and all-flash arrays',
    },
    {
      name: 'Networking',
      slug: 'networking',
      icon: '🌐',
      description: 'Data center switches, routers, and network infrastructure',
    },
    {
      name: 'Rackmount',
      slug: 'rackmount',
      icon: '🗄️',
      description: 'Server racks, PDUs, and data center accessories',
    },
    {
      name: 'Monitors',
      slug: 'monitors',
      icon: '🖥️',
      description: 'Professional displays and multi-monitor solutions',
    },
    {
      name: 'Accessories',
      slug: 'accessories',
      icon: '🔌',
      description: 'Cables, power distribution, and essential IT accessories',
    },
  ];

  const solutions = [
    {
      title: 'AI & HPC Infrastructure',
      slug: 'ai-hpc-infrastructure',
      description: 'GPU-accelerated clusters for AI training and high-performance computing',
      icon: '🚀',
    },
    {
      title: 'Data Center Builds',
      slug: 'data-center-builds',
      description: 'Complete turnkey data center solutions from design to deployment',
      icon: '🏗️',
    },
    {
      title: 'Workstation Fleets',
      slug: 'workstation-fleets',
      description: 'Enterprise workstation deployments with volume pricing',
      icon: '💼',
    },
  ];

  const trustBadges = [
    '✅ Vendor-Agnostic Solutions',
    '✅ Government Contracting',
    '✅ NAICS 334111 / 541512',
    '✅ Nationwide Delivery',
    '✅ Expert Consultation',
    '✅ Volume Pricing',
  ];

  return (
    <>
      {/* Hero Section */}
      <section className="bg-gradient-to-br from-primary to-primary-dark text-white py-20">
        <div className="container">
          <div className="max-w-4xl mx-auto text-center">
            <h1 className="text-4xl md:text-5xl lg:text-6xl font-bold mb-6">
              Enterprise IT Solutions for Government & Commercial Organizations
            </h1>
            <p className="text-xl md:text-2xl mb-8 text-blue-100">
              Servers, Workstations, Storage, Networking, and Complete Data Center Infrastructure
            </p>
            <div className="flex flex-col sm:flex-row gap-4 justify-center">
              <Link href="/government" className="btn btn-large" style={{ backgroundColor: 'white', color: 'var(--color-primary)' }}>
                Government Contracting
              </Link>
              <Link href="/contact" className="btn btn-large btn-secondary" style={{ borderColor: 'white', color: 'white' }}>
                Request a Quote
              </Link>
              <a href={`tel:${COMPANY.phoneRaw}`} className="btn btn-large btn-secondary" style={{ borderColor: 'white', color: 'white' }}>
                📞 {COMPANY.phone}
              </a>
            </div>

            {/* Trust Badges */}
            <div className="mt-12 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
              {trustBadges.map((badge, index) => (
                <div key={index} className="bg-white/10 backdrop-blur-sm rounded-lg p-3 text-sm font-semibold">
                  {badge}
                </div>
              ))}
            </div>
          </div>
        </div>
      </section>

      {/* Quick Links */}
      <section className="py-16 bg-gray-50">
        <div className="container">
          <div className="grid md:grid-cols-3 gap-8">
            <Link href="/government" className="card hover:border-primary group">
              <div className="text-4xl mb-4">🏛️</div>
              <h3 className="text-xl font-bold mb-2 group-hover:text-primary transition-colors">
                Government Contracting
              </h3>
              <p className="text-gray-600 mb-4">
                Federal, state, and local procurement. NAICS codes, certifications, and RFP support.
              </p>
              <span className="text-primary font-semibold">Learn More →</span>
            </Link>

            <Link href="/solutions/ai-hpc-infrastructure" className="card hover:border-primary group">
              <div className="text-4xl mb-4">🚀</div>
              <h3 className="text-xl font-bold mb-2 group-hover:text-primary transition-colors">
                AI & HPC Solutions
              </h3>
              <p className="text-gray-600 mb-4">
                GPU-accelerated infrastructure for machine learning and high-performance computing.
              </p>
              <span className="text-primary font-semibold">Explore Solutions →</span>
            </Link>

            <Link href="/contact" className="card hover:border-primary group">
              <div className="text-4xl mb-4">📧</div>
              <h3 className="text-xl font-bold mb-2 group-hover:text-primary transition-colors">
                Request a Quote
              </h3>
              <p className="text-gray-600 mb-4">
                Get competitive pricing for your IT project. Fast response times and expert guidance.
              </p>
              <span className="text-primary font-semibold">Contact Us →</span>
            </Link>
          </div>
        </div>
      </section>

      {/* Product Categories */}
      <section className="section">
        <div className="container">
          <div className="text-center mb-12">
            <h2 className="mb-4">Product Categories</h2>
            <p className="text-xl text-gray-600 max-w-3xl mx-auto">
              Comprehensive IT hardware solutions for enterprise and government deployments
            </p>
          </div>

          <div className="grid md:grid-cols-2 lg:grid-cols-4 gap-6">
            {productCategories.map((category) => (
              <Link
                key={category.slug}
                href={`/products/${category.slug}`}
                className="card hover:border-primary group"
              >
                <div className="text-4xl mb-3">{category.icon}</div>
                <h3 className="text-lg font-bold mb-2 group-hover:text-primary transition-colors">
                  {category.name}
                </h3>
                <p className="text-sm text-gray-600">{category.description}</p>
              </Link>
            ))}
          </div>
        </div>
      </section>

      {/* Solutions */}
      <section className="section bg-gray-50">
        <div className="container">
          <div className="text-center mb-12">
            <h2 className="mb-4">Complete Solutions</h2>
            <p className="text-xl text-gray-600 max-w-3xl mx-auto">
              Turnkey infrastructure solutions designed for your specific requirements
            </p>
          </div>

          <div className="grid md:grid-cols-3 gap-8">
            {solutions.map((solution) => (
              <Link
                key={solution.slug}
                href={`/solutions/${solution.slug}`}
                className="card hover:border-primary group bg-white"
              >
                <div className="text-5xl mb-4">{solution.icon}</div>
                <h3 className="text-xl font-bold mb-3 group-hover:text-primary transition-colors">
                  {solution.title}
                </h3>
                <p className="text-gray-600 mb-4">{solution.description}</p>
                <span className="text-primary font-semibold">Learn More →</span>
              </Link>
            ))}
          </div>
        </div>
      </section>

      {/* Why Choose Us */}
      <section className="section">
        <div className="container">
          <div className="text-center mb-12">
            <h2 className="mb-4">Why Choose PU Build?</h2>
          </div>

          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-5xl mx-auto">
            <div className="text-center">
              <div className="text-4xl mb-3">⚡</div>
              <h3 className="font-bold mb-2">Fast Response</h3>
              <p className="text-gray-600">24-hour response for general inquiries, 4-hour for RFPs</p>
            </div>

            <div className="text-center">
              <div className="text-4xl mb-3">🎯</div>
              <h3 className="font-bold mb-2">Expert Guidance</h3>
              <p className="text-gray-600">Technical consultation and solution architecture support</p>
            </div>

            <div className="text-center">
              <div className="text-4xl mb-3">🏛️</div>
              <h3 className="font-bold mb-2">Government Ready</h3>
              <p className="text-gray-600">Experienced with federal, state, and local procurement</p>
            </div>

            <div className="text-center">
              <div className="text-4xl mb-3">🔧</div>
              <h3 className="font-bold mb-2">Custom Configurations</h3>
              <p className="text-gray-600">Tailored systems to meet your exact specifications</p>
            </div>

            <div className="text-center">
              <div className="text-4xl mb-3">📦</div>
              <h3 className="font-bold mb-2">Turnkey Deployment</h3>
              <p className="text-gray-600">Pre-configured, tested, and ready to deploy</p>
            </div>

            <div className="text-center">
              <div className="text-4xl mb-3">🛡️</div>
              <h3 className="font-bold mb-2">Quality Assurance</h3>
              <p className="text-gray-600">Rigorous testing and manufacturer warranties</p>
            </div>
          </div>
        </div>
      </section>

      {/* CTA Section */}
      <section className="section bg-primary text-white">
        <div className="container text-center">
          <h2 className="mb-4">Ready to Start Your Project?</h2>
          <p className="text-xl mb-8 text-blue-100 max-w-2xl mx-auto">
            Get expert guidance and competitive pricing for your IT infrastructure needs.
            We're here to help with your next procurement.
          </p>
          <div className="flex flex-col sm:flex-row gap-4 justify-center">
            <Link href="/contact" className="btn btn-large" style={{ backgroundColor: 'white', color: 'var(--color-primary)' }}>
              Get a Quote
            </Link>
            <a href={`tel:${COMPANY.phoneRaw}`} className="btn btn-large btn-secondary" style={{ borderColor: 'white', color: 'white' }}>
              Call {COMPANY.phone}
            </a>
          </div>
        </div>
      </section>
    </>
  );
}
