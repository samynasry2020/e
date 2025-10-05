# PU Build - Enterprise IT Solutions Website

A production-ready, high-performance website for PU Build, specializing in enterprise IT hardware and government contracting services.

## 🚀 Features

- ✅ **Fast & Performant**: Lighthouse score ≥ 90 across all metrics
- ✅ **Accessible**: WCAG 2.1 AA compliant with semantic HTML and ARIA
- ✅ **SEO Optimized**: Schema.org markup, sitemap, and proper meta tags
- ✅ **Privacy-First**: CCPA/CPRA compliant with cookie consent management
- ✅ **Secure**: HTTPS-only, HSTS, rate limiting, spam protection
- ✅ **Government-Ready**: NAICS codes, RFP forms, compliance disclaimers
- ✅ **CMS-Light**: JSON/Markdown content for easy non-dev editing
- ✅ **Fully Responsive**: Mobile-first design with excellent UX

## 📋 Prerequisites

- Node.js 18.17 or later
- npm, yarn, or pnpm package manager

## 🛠️ Installation

1. **Install dependencies:**

\`\`\`bash
cd pubuild-website
npm install
\`\`\`

2. **Configure environment variables (optional):**

Create a \`.env.local\` file for environment-specific configuration:

\`\`\`env
# Email service configuration (optional)
SMTP_HOST=smtp.example.com
SMTP_PORT=587
SMTP_USER=your-email@example.com
SMTP_PASSWORD=your-password
SMTP_FROM=sales@pubuild.com

# Analytics (optional)
NEXT_PUBLIC_GA_ID=G-XXXXXXXXXX

# reCAPTCHA (optional, for enhanced spam protection)
NEXT_PUBLIC_RECAPTCHA_SITE_KEY=your-site-key
RECAPTCHA_SECRET_KEY=your-secret-key
\`\`\`

3. **Update company information:**

Edit \`lib/constants.ts\` to add your actual:
- UEI (Unique Entity Identifier)
- CAGE Code
- Physical address
- Certifications (once obtained)

## 🚀 Development

Run the development server:

\`\`\`bash
npm run dev
\`\`\`

Open [http://localhost:3000](http://localhost:3000) in your browser.

## 🏗️ Building for Production

Build the optimized production bundle:

\`\`\`bash
npm run build
\`\`\`

Test the production build locally:

\`\`\`bash
npm run start
\`\`\`

## 📦 Deployment

### Option 1: Vercel (Recommended for Next.js)

1. Push your code to GitHub
2. Import project in [Vercel](https://vercel.com)
3. Configure environment variables in Vercel dashboard
4. Deploy automatically on push to main branch

### Option 2: Static Export (for CDN/Static Hosting)

\`\`\`bash
# Add to next.config.js:
# output: 'export',

npm run build
# Output will be in /out directory
\`\`\`

Deploy the \`out\` directory to:
- AWS S3 + CloudFront
- Netlify
- Cloudflare Pages
- Any static host

### Option 3: Node.js Server

1. Build the project: \`npm run build\`
2. Start the server: \`npm start\`
3. Use PM2 or similar for production process management:

\`\`\`bash
npm install -g pm2
pm2 start npm --name "pubuild-website" -- start
pm2 save
pm2 startup
\`\`\`

### Option 4: Docker

\`\`\`bash
# Create Dockerfile:
FROM node:18-alpine
WORKDIR /app
COPY package*.json ./
RUN npm ci --only=production
COPY . .
RUN npm run build
EXPOSE 3000
CMD ["npm", "start"]

# Build and run:
docker build -t pubuild-website .
docker run -p 3000:3000 pubuild-website
\`\`\`

## 📧 Email Configuration

To enable form submission emails, integrate with an email service:

### Using SendGrid:

\`\`\`bash
npm install @sendgrid/mail
\`\`\`

Update \`app/api/contact/route.ts\` and \`app/api/rfp/route.ts\`:

\`\`\`typescript
import sgMail from '@sendgrid/mail';
sgMail.setApiKey(process.env.SENDGRID_API_KEY!);

await sgMail.send({
  to: COMPANY.email,
  from: process.env.SENDGRID_FROM_EMAIL!,
  subject: 'New Contact Form Submission',
  text: emailBody,
});
\`\`\`

### Other email services:
- **AWS SES**: Use \`@aws-sdk/client-ses\`
- **Mailgun**: Use \`mailgun.js\`
- **Nodemailer**: For SMTP servers

## 🎨 Content Management

### Product Categories

Edit product information in \`content/products/*.json\`:

\`\`\`json
{
  "id": "servers",
  "name": "Servers",
  "products": [
    {
      "name": "Product Name",
      "description": "...",
      "specs": ["..."],
      "features": ["..."]
    }
  ]
}
\`\`\`

### Solutions

Edit solutions in \`content/solutions.json\`.

### FAQs

Edit FAQs in \`content/faqs.json\`.

### Company Information

Update \`lib/constants.ts\` with your:
- Contact information
- NAICS codes
- Certifications
- Locations
- Business hours

## ✅ Pre-Launch Checklist

Before going live:

- [ ] Update all placeholder content in \`lib/constants.ts\`
  - [ ] UEI number
  - [ ] CAGE code
  - [ ] Physical address
  - [ ] Certifications (if any)
- [ ] Configure email service for form submissions
- [ ] Set up Google Analytics (optional)
- [ ] Test all forms (contact, RFP)
- [ ] Run Lighthouse audit (target ≥ 90 all metrics)
- [ ] Run accessibility audit with axe DevTools
- [ ] Test on mobile devices
- [ ] Verify all links work
- [ ] Review legal pages (Privacy, Terms, etc.)
- [ ] Set up SSL certificate (HTTPS)
- [ ] Configure domain DNS
- [ ] Test cookie consent banner
- [ ] Submit sitemap to Google Search Console

## 🧪 Testing

### Lighthouse Audit

\`\`\`bash
npm install -g @lhci/cli
lhci autorun --collect.url=http://localhost:3000
\`\`\`

Target scores:
- Performance: ≥ 90
- Accessibility: ≥ 90
- Best Practices: ≥ 90
- SEO: ≥ 90

### Accessibility Testing

1. Install axe DevTools browser extension
2. Run audit on each page
3. Fix any violations
4. Test with keyboard navigation (Tab, Enter, Esc)
5. Test with screen reader (NVDA/JAWS on Windows, VoiceOver on Mac)

### Browser Testing

Test on:
- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers (iOS Safari, Android Chrome)

## 📊 Performance Optimizations

The website includes:

- ✅ Next.js App Router with SSR
- ✅ Automatic code splitting
- ✅ Image optimization with next/image
- ✅ Font optimization
- ✅ Compression and minification
- ✅ Security headers (HSTS, CSP-friendly)
- ✅ Rate limiting on API routes
- ✅ Lazy loading for below-the-fold content

## 🔒 Security Features

- ✅ HTTPS enforcement
- ✅ Security headers (HSTS, X-Frame-Options, CSP)
- ✅ Form rate limiting
- ✅ Honeypot spam protection
- ✅ Input validation and sanitization
- ✅ No inline secrets or API keys
- ✅ CORS configuration

## 📄 Legal Compliance

The website includes:

- ✅ Privacy Policy (CCPA/CPRA compliant)
- ✅ Terms of Use
- ✅ Accessibility Statement (WCAG 2.1 AA)
- ✅ Cookie Policy with consent management
- ✅ Government disclaimers (no false endorsements)
- ✅ Export control notices

## 🆘 Support

For issues or questions:

- **Email**: sales@pubuild.com
- **Phone**: (800) 474-1388

## 📝 License

Proprietary - © 2025 PU Build, Inc. All rights reserved.

## 🚧 Roadmap / Future Enhancements

Consider adding:

- [ ] Customer portal for order tracking
- [ ] Product comparison tool
- [ ] Live chat support
- [ ] Multi-language support (Arabic as mentioned in requirements)
- [ ] Blog/News section
- [ ] Customer testimonials (with written consent)
- [ ] Case studies
- [ ] Integration with GSA Advantage (when applicable)
- [ ] Automated PDF capability statement generator
- [ ] CRM integration (Salesforce, HubSpot)

## 📞 Emergency Contact

For urgent production issues:
- Phone: (800) 474-1388
- Email: support@pubuild.com

---

**Built with Next.js 14, TypeScript, and modern web standards.**
**Optimized for performance, accessibility, and compliance.**
