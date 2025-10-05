import Link from 'next/link';
import { notFound } from 'next/navigation';
import { generatePageMetadata } from '@/lib/metadata';
import { generateBreadcrumbSchema } from '@/lib/schema';
import { COMPANY } from '@/lib/constants';
import { Solution } from '@/lib/types';
import solutionsData from '@/content/solutions.json';

const solutions = solutionsData as Solution[];

export async function generateStaticParams() {
  return solutions.map((solution) => ({
    slug: solution.slug,
  }));
}

export async function generateMetadata({ params }: { params: { slug: string } }) {
  const solution = solutions.find((s) => s.slug === params.slug);
  if (!solution) return {};

  return generatePageMetadata({
    title: solution.title,
    description: solution.description,
    path: `/solutions/${params.slug}`,
  });
}

export default function SolutionPage({ params }: { params: { slug: string } }) {
  const solution = solutions.find((s) => s.slug === params.slug);

  if (!solution) {
    notFound();
  }

  const breadcrumbSchema = generateBreadcrumbSchema([
    { name: 'Home', url: COMPANY.url },
    { name: 'Solutions', url: `${COMPANY.url}/solutions` },
    { name: solution.title, url: `${COMPANY.url}/solutions/${solution.slug}` },
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
                <span className="text-gray-600">Solutions</span>
              </li>
              <li className="text-gray-400">/</li>
              <li>
                <span className="text-gray-900 font-semibold">{solution.title}</span>
              </li>
            </ol>
          </nav>
        </div>
      </section>

      {/* Hero */}
      <section className="section bg-gradient-to-br from-primary to-primary-dark text-white">
        <div className="container">
          <div className="max-w-4xl mx-auto text-center">
            <h1 className="mb-6">{solution.title}</h1>
            <p className="text-xl text-blue-100 mb-8">{solution.description}</p>
            <div className="flex flex-col sm:flex-row gap-4 justify-center">
              <Link href="/contact" className="btn btn-large" style={{ backgroundColor: 'white', color: 'var(--color-primary)' }}>
                Request Consultation
              </Link>
              <a href={`tel:${COMPANY.phoneRaw}`} className="btn btn-large btn-secondary" style={{ borderColor: 'white', color: 'white' }}>
                📞 {COMPANY.phone}
              </a>
            </div>
          </div>
        </div>
      </section>

      {/* Features */}
      <section className="section">
        <div className="container">
          <h2 className="text-center mb-12">Solution Features</h2>
          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            {solution.features.map((feature, index) => (
              <div key={index} className="card">
                <div className="text-2xl text-primary mb-3">✓</div>
                <p className="text-gray-700">{feature}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Benefits */}
      <section className="section bg-gray-50">
        <div className="container">
          <h2 className="text-center mb-12">Key Benefits</h2>
          <div className="grid md:grid-cols-2 gap-6 max-w-4xl mx-auto">
            {solution.benefits.map((benefit, index) => (
              <div key={index} className="flex items-start bg-white p-6 rounded-lg shadow-md">
                <div className="text-2xl text-green-600 mr-4">✓</div>
                <p className="text-gray-700">{benefit}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Use Cases */}
      <section className="section">
        <div className="container">
          <h2 className="text-center mb-12">Ideal Use Cases</h2>
          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-6 max-w-5xl mx-auto">
            {solution.useCases.map((useCase, index) => (
              <div key={index} className="card bg-gradient-to-br from-blue-50 to-white border-blue-200">
                <div className="text-3xl mb-3">🎯</div>
                <p className="font-semibold text-gray-900">{useCase}</p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* CTA */}
      <section className="section bg-primary text-white">
        <div className="container text-center">
          <h2 className="mb-4">Ready to Build Your Solution?</h2>
          <p className="text-xl mb-8 text-blue-100 max-w-2xl mx-auto">
            Let's discuss your requirements and design the perfect infrastructure for your organization
          </p>
          <div className="flex flex-col sm:flex-row gap-4 justify-center">
            <Link href="/contact" className="btn btn-large" style={{ backgroundColor: 'white', color: 'var(--color-primary)' }}>
              Get Started
            </Link>
            <Link href="/government" className="btn btn-large btn-secondary" style={{ borderColor: 'white', color: 'white' }}>
              Government Contracting
            </Link>
          </div>
        </div>
      </section>
    </>
  );
}
