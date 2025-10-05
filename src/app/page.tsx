import Link from "next/link";
import { ArrowRight, Shield, Server, Cpu, HardDrive, Network, Monitor, Users } from "lucide-react";
import { Button } from "@/components/ui/Button";

export default function Home() {
  const productCategories = [
    {
      name: "Servers",
      description: "High-performance servers for enterprise and data center applications",
      icon: Server,
      href: "/products/servers",
    },
    {
      name: "Workstations",
      description: "Professional workstations for demanding computational tasks",
      icon: Cpu,
      href: "/products/workstations",
    },
    {
      name: "GPUs",
      description: "Graphics processing units for AI, HPC, and visualization",
      icon: Cpu,
      href: "/products/gpus",
    },
    {
      name: "Storage",
      description: "Enterprise storage solutions for data management and backup",
      icon: HardDrive,
      href: "/products/storage",
    },
    {
      name: "Networking",
      description: "Network infrastructure equipment and connectivity solutions",
      icon: Network,
      href: "/products/networking",
    },
    {
      name: "Monitors",
      description: "Professional displays for various applications and environments",
      icon: Monitor,
      href: "/products/monitors",
    },
  ];

  const solutions = [
    {
      name: "AI/HPC Infrastructure",
      description: "Complete infrastructure solutions for artificial intelligence and high-performance computing",
      href: "/solutions/ai-hpc",
    },
    {
      name: "Data Center Builds",
      description: "End-to-end data center design, deployment, and management services",
      href: "/solutions/data-center",
    },
    {
      name: "Workstation Fleets",
      description: "Large-scale workstation deployment and management for enterprises",
      href: "/solutions/workstation-fleets",
    },
  ];

  return (
    <div className="min-h-screen">
      {/* Skip to main content */}
      <a href="#main-content" className="skip-link">
        Skip to main content
      </a>

      {/* Hero Section */}
      <section className="bg-gradient-to-br from-blue-50 to-indigo-100 py-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center">
            <h1 className="text-4xl md:text-6xl font-bold text-gray-900 mb-6">
              IT Hardware Solutions &
              <span className="text-primary"> Government Contracting</span>
            </h1>
            <p className="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
              Leading provider of servers, workstations, GPUs, storage, and networking equipment.
              Specialized in federal government contracting with proven experience across multiple agencies.
            </p>
            <div className="flex flex-col sm:flex-row gap-4 justify-center">
              <Button size="lg" asChild>
                <Link href="/contact">
                  Get Started <ArrowRight className="ml-2 h-5 w-5" />
                </Link>
              </Button>
              <Button size="lg" variant="outline" asChild>
                <Link href="/government">
                  Government Solutions
                </Link>
              </Button>
            </div>
          </div>
        </div>
      </section>

      {/* Product Categories */}
      <section className="py-20 bg-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-16">
            <h2 className="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
              Our Products
            </h2>
            <p className="text-xl text-gray-600 max-w-2xl mx-auto">
              Comprehensive IT hardware solutions for enterprise and government applications
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            {productCategories.map((category) => {
              const IconComponent = category.icon;
              return (
                <Link
                  key={category.name}
                  href={category.href}
                  className="group bg-gray-50 rounded-lg p-8 hover:bg-gray-100 transition-all duration-300 hover:shadow-lg"
                >
                  <div className="flex items-center justify-center w-12 h-12 bg-primary rounded-lg mb-4 group-hover:bg-primary-dark transition-colors">
                    <IconComponent className="h-6 w-6 text-white" />
                  </div>
                  <h3 className="text-xl font-semibold text-gray-900 mb-2">
                    {category.name}
                  </h3>
                  <p className="text-gray-600 mb-4">
                    {category.description}
                  </p>
                  <div className="flex items-center text-primary font-medium group-hover:translate-x-2 transition-transform">
                    Learn More <ArrowRight className="ml-2 h-4 w-4" />
                  </div>
                </Link>
              );
            })}
          </div>
        </div>
      </section>

      {/* Solutions Section */}
      <section className="py-20 bg-gray-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-16">
            <h2 className="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
              Complete Solutions
            </h2>
            <p className="text-xl text-gray-600 max-w-2xl mx-auto">
              End-to-end technology solutions tailored for your specific needs
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
            {solutions.map((solution) => (
              <Link
                key={solution.name}
                href={solution.href}
                className="bg-white rounded-lg p-8 shadow-sm hover:shadow-lg transition-shadow"
              >
                <h3 className="text-xl font-semibold text-gray-900 mb-3">
                  {solution.name}
                </h3>
                <p className="text-gray-600 mb-4">
                  {solution.description}
                </p>
                <div className="flex items-center text-primary font-medium">
                  Explore Solution <ArrowRight className="ml-2 h-4 w-4" />
                </div>
              </Link>
            ))}
          </div>
        </div>
      </section>

      {/* Government Contracting Section */}
      <section className="py-20 bg-primary">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center text-white">
            <div className="flex justify-center mb-6">
              <div className="bg-white/10 rounded-full p-4">
                <Shield className="h-12 w-12 text-white" />
              </div>
            </div>
            <h2 className="text-3xl md:text-4xl font-bold mb-6">
              Government Contracting
            </h2>
            <p className="text-xl mb-8 max-w-3xl mx-auto text-blue-100">
              Trusted partner for federal agencies with extensive experience in government procurement
              processes and compliance requirements.
            </p>

            <div className="grid grid-cols-1 md:grid-cols-3 gap-8 mb-8">
              <div className="bg-white/10 rounded-lg p-6">
                <h3 className="text-lg font-semibold mb-2">NAICS Codes</h3>
                <p className="text-blue-100">
                  334111, 423430, 541512, and other relevant classifications
                </p>
              </div>
              <div className="bg-white/10 rounded-lg p-6">
                <h3 className="text-lg font-semibold mb-2">Proven Track Record</h3>
                <p className="text-blue-100">
                  Successfully delivered solutions to multiple federal agencies
                </p>
              </div>
              <div className="bg-white/10 rounded-lg p-6">
                <h3 className="text-lg font-semibold mb-2">Compliance Focused</h3>
                <p className="text-blue-100">
                  Full adherence to federal acquisition regulations and standards
                </p>
              </div>
            </div>

            <div className="flex flex-col sm:flex-row gap-4 justify-center">
              <Button size="lg" variant="secondary" asChild>
                <Link href="/government">
                  Learn About Our Government Services
                  <ArrowRight className="ml-2 h-5 w-5" />
                </Link>
              </Button>
              <Button size="lg" variant="outline" asChild>
                <Link href="/contact">
                  Start a Government Project
                </Link>
              </Button>
            </div>
          </div>
        </div>
      </section>

      {/* Trust Indicators */}
      <section className="py-16 bg-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-12">
            <h2 className="text-2xl font-bold text-gray-900 mb-4">
              Why Choose Pubuild?
            </h2>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-4 gap-8">
            <div className="text-center">
              <div className="bg-green-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                <Users className="h-8 w-8 text-green-600" />
              </div>
              <h3 className="font-semibold text-gray-900 mb-2">Expert Team</h3>
              <p className="text-gray-600 text-sm">
                Experienced professionals with deep technical knowledge
              </p>
            </div>
            <div className="text-center">
              <div className="bg-blue-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                <Shield className="h-8 w-8 text-blue-600" />
              </div>
              <h3 className="font-semibold text-gray-900 mb-2">Secure Solutions</h3>
              <p className="text-gray-600 text-sm">
                Enterprise-grade security for all your infrastructure needs
              </p>
            </div>
            <div className="text-center">
              <div className="bg-purple-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                <Server className="h-8 w-8 text-purple-600" />
              </div>
              <h3 className="font-semibold text-gray-900 mb-2">Reliable Performance</h3>
              <p className="text-gray-600 text-sm">
                High-performance hardware that you can depend on
              </p>
            </div>
            <div className="text-center">
              <div className="bg-orange-100 rounded-full w-16 h-16 flex items-center justify-center mx-auto mb-4">
                <Users className="h-8 w-8 text-orange-600" />
              </div>
              <h3 className="font-semibold text-gray-900 mb-2">24/7 Support</h3>
              <p className="text-gray-600 text-sm">
                Round-the-clock technical support for critical systems
              </p>
            </div>
          </div>
        </div>
      </section>

      {/* CTA Section */}
      <section className="py-20 bg-gray-900 text-white">
        <div className="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
          <h2 className="text-3xl md:text-4xl font-bold mb-6">
            Ready to Get Started?
          </h2>
          <p className="text-xl text-gray-300 mb-8">
            Contact us today to discuss your IT hardware needs or government contracting requirements.
          </p>
          <div className="flex flex-col sm:flex-row gap-4 justify-center">
            <Button size="lg" asChild>
              <Link href="/contact">
                Contact Us Today <ArrowRight className="ml-2 h-5 w-5" />
              </Link>
            </Button>
            <Button size="lg" variant="outline" asChild>
              <Link href="tel:8004741388">
                Call 800-474-1388
              </Link>
            </Button>
          </div>
        </div>
      </section>
    </div>
  );
}