# Pubuild Website

A modern, accessible, and high-performance website for Pubuild - IT hardware solutions and government contracting services.

## 🚀 Features

- **Next.js 15** with App Router and TypeScript
- **Tailwind CSS** for styling with custom brand colors
- **Responsive Design** that works on all devices
- **WCAG 2.1 AA Accessibility** compliance
- **SEO Optimized** with proper meta tags and schema markup
- **Privacy-First** approach with GDPR/CCPA compliance
- **Government Contracting** section with NAICS codes and certifications
- **Contact Forms** with validation and spam protection
- **RFP Intake** system for government projects
- **Multilingual Ready** (English/Arabic support)
- **Lighthouse ≥90** performance scores

## 📋 Requirements Met

### Goals ✅
- ✅ Product and solutions presentation
- ✅ Government contracting capability showcase
- ✅ Qualified lead generation (contact forms)
- ✅ Production-ready code
- ✅ Privacy-first approach
- ✅ WCAG 2.1 AA accessibility
- ✅ SEO optimization
- ✅ Lighthouse ≥90 scores

### Technical Features ✅
- ✅ Next.js with SSR and App Router
- ✅ TypeScript for type safety
- ✅ Tailwind CSS with custom utilities
- ✅ Image optimization and lazy loading
- ✅ Code splitting and HTTP/2
- ✅ Caching headers and performance optimization
- ✅ HTTPS-ready with security headers
- ✅ Rate limiting and CAPTCHA protection
- ✅ No vendor lock-in

### Pages & Sections ✅
- ✅ Home: Hero, product categories, government highlights
- ✅ Products: Category pages with specs and CTAs
- ✅ Solutions: AI/HPC, Data Center, Workstation Fleets
- ✅ Government: NAICS codes, capability statement, RFP form
- ✅ About: Mission, locations, contact info
- ✅ Contact: Lead forms and RFP intake
- ✅ Legal: Privacy Policy, Terms, Accessibility, Cookies

## 🛠️ Tech Stack

- **Framework:** Next.js 15 with App Router
- **Language:** TypeScript
- **Styling:** Tailwind CSS
- **Forms:** React Hook Form with Yup validation
- **Icons:** Lucide React
- **Deployment:** Optimized for Vercel/Netlify/static hosting
- **Analytics:** Privacy-friendly (GA4 with IP anonymization)

## 🚦 Getting Started

### Prerequisites
- Node.js 18+
- npm or yarn

### Installation

1. **Clone the repository:**
   ```bash
   git clone <repository-url>
   cd pubuild-website
   ```

2. **Install dependencies:**
   ```bash
   npm install
   ```

3. **Set up environment variables:**
   ```bash
   cp .env.example .env.local
   ```

   Edit `.env.local` with your configuration:
   ```env
   # Email service (for form submissions)
   EMAIL_SERVICE_API_KEY=your_api_key
   FROM_EMAIL=noreply@pubuild.com

   # CAPTCHA (hCaptcha recommended)
   HCAPTCHA_SITE_KEY=your_site_key
   HCAPTCHA_SECRET_KEY=your_secret_key

   # Analytics (optional)
   GA_MEASUREMENT_ID=G-XXXXXXXXXX
   ```

4. **Start development server:**
   ```bash
   npm run dev
   ```

5. **Open browser:**
   Navigate to [http://localhost:3000](http://localhost:3000)

## 🧪 Testing & Quality Assurance

### Lighthouse Audit
Run performance and accessibility audits:

```bash
# Install Lighthouse globally (one-time)
npm install -g lighthouse

# Run audit on local server
node lighthouse-audit.js http://localhost:3000

# Or run directly with npx
npx lighthouse http://localhost:3000 --output=html --output-path=lighthouse-report.html
```

**Target Scores:** ≥90 for all categories (Performance, Accessibility, Best Practices, SEO)

### Accessibility Testing
```bash
# Install axe-core CLI (one-time)
npm install -g @axe-core/cli

# Run axe accessibility tests
axe http://localhost:3000
```

## 📁 Project Structure

```
src/
├── app/                    # Next.js App Router
│   ├── (pages)            # Route groups
│   ├── globals.css        # Global styles
│   ├── layout.tsx         # Root layout
│   └── page.tsx           # Home page
├── components/            # Reusable components
│   ├── forms/            # Contact and RFP forms
│   ├── layout/           # Header, Footer, Navigation
│   └── ui/               # Base UI components
├── data/                 # Static data and content
│   ├── government.ts     # NAICS codes, certifications
│   ├── products.ts       # Product categories and info
│   └── solutions.ts      # Solution offerings
├── lib/                  # Utilities and configurations
├── providers/            # Context providers
└── types/                # TypeScript type definitions
```

## 🌐 Deployment

### Vercel (Recommended)
1. Connect your GitHub repository to Vercel
2. Set environment variables in Vercel dashboard
3. Deploy automatically on push to main branch

### Other Platforms
The site is optimized for static hosting and can be deployed to:
- **Netlify:** Drag and drop the build folder
- **Static hosting:** Use `npm run build && npm run export`
- **Traditional hosting:** Copy the `.next` folder

### Build Commands
```bash
# Development build
npm run build

# Production build
npm run build

# Export static files (if needed)
npm run export
```

## 🔧 Content Management

### Updating Products
Edit `src/data/products.ts` to modify product categories and information.

### Government Information
Update `src/data/government.ts` for NAICS codes, certifications, and capability statements.

### Adding New Pages
1. Create new route in `src/app/`
2. Add navigation links in `Header.tsx`
3. Update sitemap if needed

## 🔒 Security Features

- **Rate Limiting:** Form submissions are rate-limited
- **CAPTCHA:** hCaptcha integration for spam protection
- **Input Validation:** Server and client-side validation
- **HTTPS Ready:** Configured for SSL/TLS
- **Security Headers:** XSS protection, CSRF tokens
- **Privacy Controls:** Cookie consent and data deletion

## ♿ Accessibility Features

- **WCAG 2.1 AA Compliant**
- **Keyboard Navigation:** Full keyboard accessibility
- **Screen Reader Support:** Semantic HTML and ARIA labels
- **Color Contrast:** Meets accessibility standards
- **Focus Management:** Clear focus indicators
- **Skip Links:** Quick navigation to main content
- **Alternative Text:** Descriptive image alt text

## 🌍 Internationalization

Ready for multilingual support:
- Arabic RTL styling prepared
- Server-side routing (`/ar/...`)
- Translation files structure ready
- Language toggle in header

## 📊 Analytics & Monitoring

Privacy-friendly analytics setup:
- Google Analytics 4 with IP anonymization
- Cookie consent integration
- Server-side tracking capabilities
- Performance monitoring ready

## 🤝 Contributing

1. Follow TypeScript and ESLint guidelines
2. Maintain accessibility standards
3. Test changes with Lighthouse
4. Update documentation for new features

## 📞 Support

For technical support or questions:
- **Email:** support@pubuild.com
- **Phone:** 800-474-1388
- **Response Time:** 24 hours for general inquiries

## 📜 Legal Compliance

- **Privacy Policy:** GDPR/CCPA compliant
- **Terms of Use:** Clear usage terms
- **Accessibility Statement:** WCAG compliance commitment
- **Cookie Policy:** Transparent cookie usage
- **Government Compliance:** Federal acquisition regulation awareness

---

**Built with ❤️ for Pubuild - Your trusted IT hardware and government contracting partner.**