import Layout from '@/components/layout/layout'
import Hero from '@/components/sections/hero'
import ProductCategories from '@/components/sections/product-categories'
import Solutions from '@/components/sections/solutions'
import GovernmentSection from '@/components/sections/government-section'
import TrustBadges from '@/components/sections/trust-badges'

export default function Home() {
  return (
    <Layout>
      <Hero />
      <ProductCategories />
      <Solutions />
      <GovernmentSection />
      <TrustBadges />
    </Layout>
  )
}