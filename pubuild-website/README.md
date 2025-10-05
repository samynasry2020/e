# Pubuild Website

A fast, accessible, legally-compliant company website for Pubuild - IT hardware solutions and government contracting services.

## Features

- **Next.js 15** with App Router and TypeScript
- **Tailwind CSS** for styling with custom design system
- **Accessibility** - WCAG 2.1 AA compliant
- **SEO Optimized** - Meta tags, sitemap, structured data
- **Government Contracting** - NAICS codes, compliance documentation
- **Responsive Design** - Mobile-first approach
- **Performance** - Optimized for Lighthouse scores ≥ 90

## Tech Stack

- Next.js 15 (App Router)
- TypeScript
- Tailwind CSS
- Lucide React (icons)
- Framer Motion (animations)
- Headless UI (components)

## Getting Started

### Prerequisites

- Node.js 18+ 
- npm or yarn

### Installation

1. Clone the repository:
```bash
git clone <repository-url>
cd pubuild-website
```

2. Install dependencies:
```bash
npm install
```

3. Run the development server:
```bash
npm run dev
```

4. Open [http://localhost:3000](http://localhost:3000) in your browser.

## Deployment

### Vercel (Recommended)

1. Push your code to GitHub
2. Connect your repository to Vercel
3. Deploy automatically

### Other Platforms

The app can be deployed to any platform that supports Next.js:

- Netlify
- AWS Amplify
- Railway
- DigitalOcean App Platform

### Build for Production

```bash
npm run build
npm start
```

## Project Structure

```
src/
├── app/                    # Next.js App Router pages
│   ├── about/             # About page
│   ├── contact/           # Contact page with forms
│   ├── government/        # Government contracting
│   ├── products/          # Product pages
│   ├── solutions/         # Solution pages
│   ├── resources/         # Resources and documentation
│   ├── privacy/           # Legal pages
│   ├── terms/
│   ├── accessibility/
│   └── cookies/
├── components/            # React components
│   ├── layout/           # Header, footer, layout
│   ├── sections/         # Page sections
│   └── ui/               # Reusable UI components
└── lib/                  # Utilities and helpers
```

## Key Pages

- **Home** - Hero section, product categories, solutions overview
- **Products** - IT hardware categories (servers, workstations, GPUs, etc.)
- **Solutions** - Enterprise solutions (AI/HPC, data centers, workstation fleets)
- **Government** - Contracting services, NAICS codes, RFP support
- **Contact** - Lead generation forms with validation
- **Legal** - Privacy policy, terms, accessibility statement

## Compliance Features

- **WCAG 2.1 AA** - Accessibility compliance
- **Privacy First** - GDPR/CCPA compliant privacy policy
- **Government Ready** - NAICS codes, export controls, security standards
- **Legal Pages** - Complete legal documentation

## SEO Features

- Meta tags and Open Graph
- Structured data (Schema.org)
- XML sitemap
- Robots.txt
- Canonical URLs
- Performance optimization

## Customization

### Brand Colors

Update colors in `tailwind.config.ts`:

```typescript
colors: {
  primary: {
    DEFAULT: '#1e40af',    // Blue
    foreground: '#ffffff',
  },
  secondary: {
    DEFAULT: '#3b82f6',    // Light blue
    foreground: '#ffffff',
  },
}
```

### Content Updates

- Product information: Update in respective page files
- Company information: Update in `src/components/layout/`
- Legal content: Update in respective legal page files

## Performance

The website is optimized for performance with:

- Image optimization
- Code splitting
- Lazy loading
- Caching headers
- Minimal bundle size

Target Lighthouse scores:
- Performance: ≥ 90
- Accessibility: ≥ 90
- SEO: ≥ 90
- Best Practices: ≥ 90

## Security

- HTTPS only
- HSTS headers
- Form validation
- Rate limiting (implement on server)
- No inline secrets
- Secure headers

## Support

For technical support or questions:

- Email: support@pubuild.com
- Phone: 800-474-1388
- Website: https://pubuild.com

## License

© 2024 Pubuild. All rights reserved.

## Legal Notice

We are an independent supplier. No government endorsement implied.
Products may be subject to U.S. export controls; purchaser is responsible for compliance.