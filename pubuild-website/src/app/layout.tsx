import type { Metadata } from 'next'
import { Inter } from 'next/font/google'
import './globals.css'

const inter = Inter({ subsets: ['latin'] })

export const metadata: Metadata = {
  title: 'Pubuild - IT Hardware Solutions & Government Contracting',
  description: 'Professional IT hardware solutions for government agencies and enterprises. Servers, workstations, GPUs, networking equipment. NAICS certified, compliance ready.',
  keywords: 'government IT bids, servers for agencies, rackmount servers, AI/HPC infrastructure, NAICS 334111, 541512, government contracting, IT hardware',
  authors: [{ name: 'Pubuild' }],
  creator: 'Pubuild',
  publisher: 'Pubuild',
  formatDetection: {
    email: false,
    address: false,
    telephone: false,
  },
  metadataBase: new URL('https://pubuild.com'),
  alternates: {
    canonical: '/',
  },
  openGraph: {
    title: 'Pubuild - IT Hardware Solutions & Government Contracting',
    description: 'Professional IT hardware solutions for government agencies and enterprises. Servers, workstations, GPUs, networking equipment.',
    url: 'https://pubuild.com',
    siteName: 'Pubuild',
    images: [
      {
        url: '/logo.svg',
        width: 200,
        height: 60,
        alt: 'Pubuild Logo',
      },
    ],
    locale: 'en_US',
    type: 'website',
  },
  twitter: {
    card: 'summary_large_image',
    title: 'Pubuild - IT Hardware Solutions & Government Contracting',
    description: 'Professional IT hardware solutions for government agencies and enterprises.',
    images: ['/logo.svg'],
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
  verification: {
    google: 'your-google-verification-code',
  },
}

export default function RootLayout({
  children,
}: {
  children: React.ReactNode
}) {
  return (
    <html lang="en">
      <head>
        <link rel="icon" href="/favicon.ico" />
        <link rel="canonical" href="https://pubuild.com" />
        <script
          type="application/ld+json"
          dangerouslySetInnerHTML={{
            __html: JSON.stringify({
              "@context": "https://schema.org",
              "@type": "Organization",
              "name": "Pubuild",
              "url": "https://pubuild.com",
              "logo": "https://pubuild.com/logo.svg",
              "description": "Professional IT hardware solutions for government agencies and enterprises",
              "address": {
                "@type": "PostalAddress",
                "addressCountry": "US",
                "addressRegion": "CA"
              },
              "contactPoint": {
                "@type": "ContactPoint",
                "telephone": "+1-800-474-1388",
                "contactType": "customer service",
                "email": "sales@pubuild.com"
              },
              "sameAs": [
                "https://pubuild.com"
              ]
            })
          }}
        />
      </head>
      <body className={inter.className}>
        {children}
      </body>
    </html>
  )
}