import { Metadata } from 'next';
import { COMPANY, SEO_KEYWORDS } from './constants';

export function generatePageMetadata({
  title,
  description,
  keywords = [],
  path = '',
  image = '/logo.svg',
}: {
  title: string;
  description: string;
  keywords?: string[];
  path?: string;
  image?: string;
}): Metadata {
  const fullTitle = title === COMPANY.name ? title : `${title} | ${COMPANY.name}`;
  const url = `${COMPANY.url}${path}`;
  const allKeywords = [...SEO_KEYWORDS, ...keywords].join(', ');

  return {
    title: fullTitle,
    description,
    keywords: allKeywords,
    authors: [{ name: COMPANY.name }],
    creator: COMPANY.name,
    publisher: COMPANY.name,
    metadataBase: new URL(COMPANY.url),
    alternates: {
      canonical: url,
    },
    openGraph: {
      title: fullTitle,
      description,
      url,
      siteName: COMPANY.name,
      images: [
        {
          url: image,
          width: 1200,
          height: 630,
          alt: COMPANY.name,
        },
      ],
      locale: 'en_US',
      type: 'website',
    },
    twitter: {
      card: 'summary_large_image',
      title: fullTitle,
      description,
      images: [image],
    },
    robots: {
      index: true,
      follow: true,
      googleBot: {
        index: true,
        follow: true,
        'max-video-preview': -1,
        'max-image-preview': 'large',
        'max-snippet': -1,
      },
    },
  };
}
