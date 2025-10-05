import Link from 'next/link';
import { notFound } from 'next/navigation';
import { generatePageMetadata } from '@/lib/metadata';
import { generateBreadcrumbSchema, generateProductSchema } from '@/lib/schema';
import { COMPANY } from '@/lib/constants';
import { ProductCategory } from '@/lib/types';

// Import product data
import serversData from '@/content/products/servers.json';
import workstationsData from '@/content/products/workstations.json';
import gpusData from '@/content/products/gpus.json';
import storageData from '@/content/products/storage.json';
import networkingData from '@/content/products/networking.json';
import rackmountData from '@/content/products/rackmount.json';
import monitorsData from '@/content/products/monitors.json';
import accessoriesData from '@/content/products/accessories.json';

const allProducts: ProductCategory[] = [
  serversData,
  workstationsData,
  gpusData,
  storageData,
  networkingData,
  rackmountData,
  monitorsData,
  accessoriesData,
] as ProductCategory[];

export async function generateStaticParams() {
  return allProducts.map((product) => ({
    slug: product.slug,
  }));
}

export async function generateMetadata({ params }: { params: { slug: string } }) {
  const category = allProducts.find((p) => p.slug === params.slug);
  if (!category) return {};

  return generatePageMetadata({
    title: category.name,
    description: category.description,
    path: `/products/${params.slug}`,
    keywords: [category.name, 'enterprise', 'IT hardware', 'government contracting'],
  });
}

export default function ProductCategoryPage({ params }: { params: { slug: string } }) {
  const category = allProducts.find((p) => p.slug === params.slug);

  if (!category) {
    notFound();
  }

  const breadcrumbSchema = generateBreadcrumbSchema([
    { name: 'Home', url: COMPANY.url },
    { name: 'Products', url: `${COMPANY.url}/products` },
    { name: category.name, url: `${COMPANY.url}/products/${category.slug}` },
  ]);

  return (
    <>
      <script
        type="application/ld+json"
        dangerouslySetInnerHTML={{ __html: JSON.stringify(breadcrumbSchema) }}
      />

      {/* Breadcrumb */}
      <section className="bg-gray-50 py-4 border-b">
        <div className="container">
          <nav aria-label="Breadcrumb">
            <ol className="flex items-center space-x-2 text-sm">
              <li>
                <Link href="/" className="text-gray-600 hover:text-primary">
                  Home
                </Link>
              </li>
              <li className="text-gray-400">/</li>
              <li>
                <span className="text-gray-600">Products</span>
              </li>
              <li className="text-gray-400">/</li>
              <li>
                <span className="text-gray-900 font-semibold">{category.name}</span>
              </li>
            </ol>
          </nav>
        </div>
      </section>

      {/* Hero */}
      <section className="section bg-gradient-to-br from-gray-50 to-white">
        <div className="container">
          <div className="max-w-4xl">
            <div className="text-5xl mb-4">{category.icon}</div>
            <h1 className="mb-4">{category.name}</h1>
            <p className="text-xl text-gray-600 mb-6">{category.description}</p>
            <div className="flex gap-4">
              <Link href="/contact" className="btn btn-primary">
                Request a Quote
              </Link>
              <a href={`tel:${COMPANY.phoneRaw}`} className="btn btn-secondary">
                📞 {COMPANY.phone}
              </a>
            </div>
          </div>
        </div>
      </section>

      {/* Products */}
      <section className="section">
        <div className="container">
          <h2 className="mb-8">Available Products</h2>
          <div className="space-y-12">
            {category.products?.map((product) => (
              <div key={product.id} className="card">
                <div className="grid md:grid-cols-3 gap-6">
                  <div className="md:col-span-2">
                    <h3 className="text-2xl font-bold mb-3">{product.name}</h3>
                    <p className="text-gray-700 mb-4">{product.description}</p>

                    {/* Specifications */}
                    {product.specs && product.specs.length > 0 && (
                      <div className="mb-4">
                        <h4 className="font-semibold mb-2">Key Specifications:</h4>
                        <ul className="space-y-1">
                          {product.specs.map((spec, index) => (
                            <li key={index} className="text-sm text-gray-700 flex items-start">
                              <span className="text-primary mr-2">•</span>
                              {spec}
                            </li>
                          ))}
                        </ul>
                      </div>
                    )}

                    {/* Features */}
                    {product.features && product.features.length > 0 && (
                      <div className="mb-4">
                        <h4 className="font-semibold mb-2">Features:</h4>
                        <div className="flex flex-wrap gap-2">
                          {product.features.map((feature, index) => (
                            <span
                              key={index}
                              className="bg-blue-50 text-blue-700 px-3 py-1 rounded-full text-sm"
                            >
                              {feature}
                            </span>
                          ))}
                        </div>
                      </div>
                    )}

                    {/* Compatibility */}
                    {product.compatibility && product.compatibility.length > 0 && (
                      <div className="mb-4">
                        <h4 className="font-semibold mb-2">Compatibility:</h4>
                        <ul className="space-y-1">
                          {product.compatibility.map((item, index) => (
                            <li key={index} className="text-sm text-gray-600">
                              ✓ {item}
                            </li>
                          ))}
                        </ul>
                      </div>
                    )}
                  </div>

                  {/* Applications */}
                  <div>
                    {product.applications && product.applications.length > 0 && (
                      <div className="bg-gray-50 p-4 rounded-lg">
                        <h4 className="font-semibold mb-3">Ideal For:</h4>
                        <ul className="space-y-2">
                          {product.applications.map((app, index) => (
                            <li key={index} className="text-sm text-gray-700 flex items-start">
                              <span className="text-green-600 mr-2">✓</span>
                              {app}
                            </li>
                          ))}
                        </ul>
                      </div>
                    )}

                    <div className="mt-4">
                      <Link
                        href={`/contact?product=${encodeURIComponent(product.name)}`}
                        className="btn btn-primary w-full text-center"
                      >
                        Get a Quote
                      </Link>
                    </div>
                  </div>
                </div>

                <script
                  type="application/ld+json"
                  dangerouslySetInnerHTML={{ __html: JSON.stringify(generateProductSchema(product)) }}
                />
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* CTA */}
      <section className="section bg-gray-50">
        <div className="container text-center">
          <h2 className="mb-4">Need Help Choosing?</h2>
          <p className="text-xl text-gray-600 mb-6 max-w-2xl mx-auto">
            Our team can help you select the right configuration for your specific requirements
          </p>
          <div className="flex flex-col sm:flex-row gap-4 justify-center">
            <Link href="/contact" className="btn btn-primary btn-large">
              Contact Our Team
            </Link>
            <Link href="/government" className="btn btn-secondary btn-large">
              Government Contracting Info
            </Link>
          </div>
        </div>
      </section>
    </>
  );
}
