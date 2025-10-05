import type { Metadata } from 'next';
import './globals.css';
import Header from '@/components/Header';
import Footer from '@/components/Footer';
import CookieBanner from '@/components/CookieBanner';
import { generateOrganizationSchema } from '@/lib/schema';
import { COMPANY } from '@/lib/constants';

export const metadata: Metadata = {
  metadataBase: new URL(COMPANY.url),
  title: {
    default: `${COMPANY.name} - Enterprise IT Solutions & Government Contracting`,
    template: `%s | ${COMPANY.name}`,
  },
  description: 'Enterprise IT hardware solutions including servers, workstations, GPUs, storage, and networking. Serving government agencies and commercial organizations nationwide.',
  keywords: 'enterprise servers, workstations, GPU servers, data center, government IT, NAICS 334111, federal contracting',
  authors: [{ name: COMPANY.name }],
  creator: COMPANY.name,
  publisher: COMPANY.name,
  formatDetection: {
    email: false,
    address: false,
    telephone: false,
  },
  openGraph: {
    type: 'website',
    locale: 'en_US',
    url: COMPANY.url,
    siteName: COMPANY.name,
    title: `${COMPANY.name} - Enterprise IT Solutions`,
    description: 'Enterprise IT hardware and government contracting services',
  },
  twitter: {
    card: 'summary_large_image',
    title: `${COMPANY.name} - Enterprise IT Solutions`,
    description: 'Enterprise IT hardware and government contracting services',
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
  icons: {
    icon: '/favicon.svg',
    shortcut: '/favicon.svg',
    apple: '/favicon.svg',
  },
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  const organizationSchema = generateOrganizationSchema();

  return (
    <html lang="en">
      <head>
        <script
          type="application/ld+json"
          dangerouslySetInnerHTML={{ __html: JSON.stringify(organizationSchema) }}
        />
      </head>
      <body>
        <Header />
        <main id="main-content" className="min-h-screen">
          {children}
        </main>
        <Footer />
        <CookieBanner />
      </body>
    </html>
  );
}
