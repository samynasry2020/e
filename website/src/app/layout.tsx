import type { Metadata } from "next";
import { Geist, Geist_Mono } from "next/font/google";
import "./globals.css";
import { Header } from "@/components/Header";
import { Footer } from "@/components/Footer";
import { siteConfig } from "@/lib/config";
import { CookieBanner } from "@/components/CookieBanner";
import { Analytics } from "@/components/Analytics";

const geistSans = Geist({
  variable: "--font-geist-sans",
  subsets: ["latin"],
});

const geistMono = Geist_Mono({
  variable: "--font-geist-mono",
  subsets: ["latin"],
});

export const metadata: Metadata = {
  metadataBase: new URL(siteConfig.baseUrl),
  title: {
    default: `${siteConfig.siteName} — IT Hardware & Government Contracting`,
    template: `%s — ${siteConfig.siteName}`,
  },
  description:
    "IT hardware: servers, workstations, rackmount, GPUs, storage, networking. Government contracting support for U.S. agencies.",
  applicationName: siteConfig.siteName,
  alternates: { canonical: "/" },
  manifest: "/manifest.json",
  openGraph: {
    type: "website",
    url: siteConfig.baseUrl,
    title: `${siteConfig.siteName} — IT Hardware & Government Contracting`,
    description:
      "Servers, workstations, rackmount, GPUs, storage, networking. Government contracting support for U.S. agencies.",
    siteName: siteConfig.siteName,
  },
  twitter: {
    card: "summary_large_image",
    title: `${siteConfig.siteName} — IT Hardware & Government Contracting`,
    description:
      "Servers, workstations, rackmount, GPUs, storage, networking. Government contracting support for U.S. agencies.",
  },
  keywords: [
    "government IT bids",
    "servers for agencies",
    "rackmount servers",
    "AI/HPC infrastructure",
    "NAICS 334111",
    "541512",
  ],
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="en">
      <body
        className={`${geistSans.variable} ${geistMono.variable} antialiased bg-[--color-background] text-[--color-foreground]`}
      >
        <Header />
        <main id="main" className="min-h-[60vh]">
          {children}
        </main>
        <Footer />
        {/* No third-party logos unless permission; analytics opt-in configured via cookie banner. */}
        <script
          type="application/ld+json"
          // eslint-disable-next-line react/no-danger
          dangerouslySetInnerHTML={{
            __html: JSON.stringify({
              '@context': 'https://schema.org',
              '@type': 'Organization',
              name: siteConfig.siteName,
              url: siteConfig.baseUrl,
              contactPoint: [
                {
                  '@type': 'ContactPoint',
                  contactType: 'customer service',
                  email: siteConfig.contact.salesEmail,
                  telephone: `+1-${siteConfig.contact.phone}`,
                  areaServed: 'US',
                  availableLanguage: ['en'],
                },
              ],
            }),
          }}
        />
        <CookieBanner />
        <Analytics />
      </body>
    </html>
  );
}
