import { notFound } from "next/navigation";
import Link from "next/link";
import { ArrowRight, Check } from "lucide-react";
import { productCategories } from "@/data/products";
import { Button } from "@/components/ui/Button";

interface ProductCategoryPageProps {
  params: {
    slug: string;
  };
}

export async function generateStaticParams() {
  return productCategories.map((category) => ({
    slug: category.slug,
  }));
}

export async function generateMetadata({ params }: ProductCategoryPageProps) {
  const category = productCategories.find((cat) => cat.slug === params.slug);

  if (!category) {
    return {
      title: "Product Not Found",
    };
  }

  return {
    title: `${category.name} - IT Hardware Solutions`,
    description: category.description,
  };
}

export default function ProductCategoryPage({ params }: ProductCategoryPageProps) {
  const category = productCategories.find((cat) => cat.slug === params.slug);

  if (!category) {
    notFound();
  }

  return (
    <div className="min-h-screen bg-gray-50">
      {/* Hero Section */}
      <section className="bg-white py-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center">
            <div className="bg-primary/10 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-6">
              <div className="w-10 h-10 bg-primary rounded"></div>
            </div>
            <h1 className="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
              {category.name}
            </h1>
            <p className="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
              {category.description}
            </p>
            <Button size="lg" asChild>
              <Link href="/contact">
                Get a Quote <ArrowRight className="ml-2 h-5 w-5" />
              </Link>
            </Button>
          </div>
        </div>
      </section>

      {/* Features Section */}
      <section className="py-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="grid grid-cols-1 lg:grid-cols-2 gap-16">
            <div>
              <h2 className="text-3xl font-bold text-gray-900 mb-8">
                Key Features & Capabilities
              </h2>
              <div className="space-y-4">
                {category.features.map((feature, index) => (
                  <div key={index} className="flex items-start space-x-3">
                    <Check className="h-6 w-6 text-green-600 mt-0.5 flex-shrink-0" />
                    <p className="text-gray-700">{feature}</p>
                  </div>
                ))}
              </div>
            </div>

            <div className="bg-white rounded-lg shadow-sm p-8">
              <h3 className="text-xl font-semibold text-gray-900 mb-6">
                Why Choose Our {category.name}?
              </h3>
              <div className="space-y-4 text-gray-600">
                <p>
                  Our {category.name.toLowerCase()} are carefully selected and tested to meet
                  the demanding requirements of enterprise and government applications.
                </p>
                <p>
                  We work with leading manufacturers to ensure you get reliable, high-performance
                  hardware that fits your budget and technical requirements.
                </p>
                <p>
                  Every solution includes our comprehensive support and warranty coverage,
                  giving you peace of mind for the entire lifecycle of your investment.
                </p>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Specifications Section (if available) */}
      {category.specifications && (
        <section className="py-20 bg-white">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <h2 className="text-3xl font-bold text-gray-900 mb-8 text-center">
              Technical Specifications
            </h2>
            <div className="bg-gray-50 rounded-lg p-8">
              <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                {Object.entries(category.specifications).map(([key, value]) => (
                  <div key={key} className="flex justify-between py-2 border-b border-gray-200">
                    <span className="font-medium text-gray-900">{key}:</span>
                    <span className="text-gray-700">{value}</span>
                  </div>
                ))}
              </div>
            </div>
          </div>
        </section>
      )}

      {/* CTA Section */}
      <section className="py-20 bg-primary">
        <div className="max-w-4xl mx-auto text-center px-4 sm:px-6 lg:px-8">
          <h2 className="text-3xl font-bold text-white mb-6">
            Ready to Discuss Your {category.name} Requirements?
          </h2>
          <p className="text-xl text-blue-100 mb-8">
            Our technical experts are ready to help you find the perfect solution for your needs.
          </p>
          <div className="flex flex-col sm:flex-row gap-4 justify-center">
            <Button size="lg" variant="secondary" asChild>
              <Link href="/contact">
                Contact Our Experts <ArrowRight className="ml-2 h-5 w-5" />
              </Link>
            </Button>
            <Button size="lg" variant="outline" asChild>
              <Link href="/products">
                View All Products
              </Link>
            </Button>
          </div>
        </div>
      </section>
    </div>
  );
}