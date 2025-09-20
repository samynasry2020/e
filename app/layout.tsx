import type { Metadata } from 'next'
import { Inter } from 'next/font/google'
import './globals.css'
import Header from './components/Header'
import Footer from './components/Footer'

const inter = Inter({ subsets: ['latin'] })

export const metadata: Metadata = {
  title: 'PUBUILD TECHNOLOGIES INC. - Government Server Solutions',
  description: 'Professional server building, setup, manufacturing, and design services exclusively for US Government and government agencies. Expert solutions for federal contracts and government bidding.',
  keywords: 'government servers, federal contracts, server manufacturing, government technology, US government services, server design, government bidding',
  authors: [{ name: 'PUBUILD TECHNOLOGIES INC.' }],
  openGraph: {
    title: 'PUBUILD TECHNOLOGIES INC. - Government Server Solutions',
    description: 'Professional server solutions exclusively for US Government agencies',
    url: 'https://pubuild.com',
    siteName: 'PUBUILD TECHNOLOGIES INC.',
    locale: 'en_US',
    type: 'website',
  },
  robots: {
    index: true,
    follow: true,
  },
}

export default function RootLayout({
  children,
}: {
  children: React.ReactNode
}) {
  return (
    <html lang="en">
      <body className={inter.className}>
        <Header />
        <main>{children}</main>
        <Footer />
      </body>
    </html>
  )
}