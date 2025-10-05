import type { Metadata } from "next";
import { Inter } from "next/font/google";
import "./globals.css";
import { Header } from "@/components/layout/Header";
import { Footer } from "@/components/layout/Footer";
import { CookieBanner } from "@/components/CookieBanner";
import { AccessibilityProvider } from "@/providers/AccessibilityProvider";

const inter = Inter({
  subsets: ["latin"],
  variable: "--font-inter",
});

export const metadata: Metadata = {
  title: {
    default: "Pubuild - IT Hardware Solutions & Government Contracting",
    template: "%s | Pubuild",
  },
  description: "Leading provider of IT hardware solutions including servers, workstations, GPUs, storage, and networking equipment. Specialized in government contracting with NAICS codes 334111, 423430, 541512.",
  keywords: ["IT hardware", "servers", "workstations", "GPUs", "government contracting", "NAICS 334111", "NAICS 423430", "NAICS 541512", "rackmount servers", "AI infrastructure"],
  authors: [{ name: "Pubuild" }],
  creator: "Pubuild",
  publisher: "Pubuild",
  formatDetection: {
    email: false,
    address: false,
    telephone: false,
  },
  metadataBase: new URL("https://pubuild.com"),
  alternates: {
    canonical: "/",
  },
  openGraph: {
    type: "website",
    locale: "en_US",
    url: "https://pubuild.com",
    title: "Pubuild - IT Hardware Solutions & Government Contracting",
    description: "Leading provider of IT hardware solutions including servers, workstations, GPUs, storage, and networking equipment. Specialized in government contracting.",
    siteName: "Pubuild",
  },
  twitter: {
    card: "summary_large_image",
    title: "Pubuild - IT Hardware Solutions & Government Contracting",
    description: "Leading provider of IT hardware solutions including servers, workstations, GPUs, storage, and networking equipment. Specialized in government contracting.",
  },
  robots: {
    index: true,
    follow: true,
    googleBot: {
      index: true,
      follow: true,
      "max-video-preview": -1,
      "max-image-preview": "large",
      "max-snippet": -1,
    },
  },
  verification: {
    google: "your-google-verification-code",
  },
};

export default function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <html lang="en" className={inter.variable}>
      <body className="font-sans antialiased">
        <AccessibilityProvider>
          <div className="min-h-screen flex flex-col">
            <Header />
            <main className="flex-1" id="main-content">
              {children}
            </main>
            <Footer />
          </div>
          <CookieBanner />
        </AccessibilityProvider>
      </body>
    </html>
  );
}