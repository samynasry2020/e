// Schema.org JSON-LD generators for SEO
import { COMPANY } from './constants';
import { Product, FAQItem } from './types';

export function generateOrganizationSchema() {
  return {
    '@context': 'https://schema.org',
    '@type': 'Organization',
    name: COMPANY.name,
    legalName: COMPANY.legalName,
    url: COMPANY.url,
    logo: `${COMPANY.url}/logo.svg`,
    contactPoint: [
      {
        '@type': 'ContactPoint',
        telephone: COMPANY.phone,
        contactType: 'customer service',
        email: COMPANY.email,
        areaServed: 'US',
        availableLanguage: 'English',
      },
      {
        '@type': 'ContactPoint',
        telephone: COMPANY.phone,
        contactType: 'sales',
        email: COMPANY.email,
        areaServed: 'US',
        availableLanguage: 'English',
      },
    ],
    address: COMPANY.locations[0] ? {
      '@type': 'PostalAddress',
      streetAddress: COMPANY.locations[0].address,
      addressLocality: COMPANY.locations[0].city,
      addressRegion: COMPANY.locations[0].state,
      postalCode: COMPANY.locations[0].zip,
      addressCountry: 'US',
    } : undefined,
    sameAs: Object.values(COMPANY).filter(v => typeof v === 'string' && v.startsWith('http')),
  };
}

export function generateProductSchema(product: Product) {
  return {
    '@context': 'https://schema.org',
    '@type': 'Product',
    name: product.name,
    description: product.description,
    category: product.category,
    brand: {
      '@type': 'Brand',
      name: COMPANY.name,
    },
    offers: {
      '@type': 'Offer',
      availability: 'https://schema.org/InStock',
      priceCurrency: 'USD',
      seller: {
        '@type': 'Organization',
        name: COMPANY.name,
      },
    },
  };
}

export function generateFAQSchema(faqs: FAQItem[]) {
  return {
    '@context': 'https://schema.org',
    '@type': 'FAQPage',
    mainEntity: faqs.map(faq => ({
      '@type': 'Question',
      name: faq.question,
      acceptedAnswer: {
        '@type': 'Answer',
        text: faq.answer,
      },
    })),
  };
}

export function generateBreadcrumbSchema(items: { name: string; url: string }[]) {
  return {
    '@context': 'https://schema.org',
    '@type': 'BreadcrumbList',
    itemListElement: items.map((item, index) => ({
      '@type': 'ListItem',
      position: index + 1,
      name: item.name,
      item: item.url,
    })),
  };
}

export function generateLocalBusinessSchema() {
  const location = COMPANY.locations[0];
  if (!location) return null;

  return {
    '@context': 'https://schema.org',
    '@type': 'LocalBusiness',
    name: COMPANY.name,
    image: `${COMPANY.url}/logo.svg`,
    '@id': COMPANY.url,
    url: COMPANY.url,
    telephone: COMPANY.phone,
    email: COMPANY.email,
    address: {
      '@type': 'PostalAddress',
      streetAddress: location.address,
      addressLocality: location.city,
      addressRegion: location.state,
      postalCode: location.zip,
      addressCountry: 'US',
    },
    openingHoursSpecification: [
      {
        '@type': 'OpeningHoursSpecification',
        dayOfWeek: ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday'],
        opens: '08:00',
        closes: '18:00',
      },
    ],
  };
}
