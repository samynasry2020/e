# Deployment Guide for PU Build Website

Complete step-by-step deployment instructions for production.

## Pre-Deployment Checklist

### 1. Content Configuration

- [ ] Update `/lib/constants.ts` with actual values:
  ```typescript
  uei: '[YOUR-ACTUAL-UEI]',
  cage: '[YOUR-ACTUAL-CAGE-CODE]',
  duns: '[YOUR-ACTUAL-DUNS]',
  ```

- [ ] Add physical address in `COMPANY.locations`

- [ ] Add certifications once obtained (or leave empty array)

- [ ] Review and update product categories in `/content/products/*.json`

- [ ] Update solutions in `/content/solutions.json`

- [ ] Review FAQs in `/content/faqs.json`

### 2. Email Service Setup

Choose and configure one email service for form submissions:

#### Option A: SendGrid (Recommended)

1. Sign up at [sendgrid.com](https://sendgrid.com)
2. Create an API key
3. Verify your sender email (sales@pubuild.com)
4. Install SDK: `npm install @sendgrid/mail`
5. Update API routes to use SendGrid

#### Option B: AWS SES

1. Set up AWS SES in your AWS account
2. Verify domain and email addresses
3. Request production access (initially in sandbox)
4. Install SDK: `npm install @aws-sdk/client-ses`
5. Configure AWS credentials

#### Option C: Custom SMTP

1. Get SMTP credentials from your email provider
2. Install: `npm install nodemailer`
3. Configure SMTP settings in environment variables

### 3. Environment Variables

Create `.env.production` or configure in your hosting platform:

```env
# Email (choose one)
SENDGRID_API_KEY=SG.xxxx
SENDGRID_FROM_EMAIL=sales@pubuild.com

# Analytics (optional)
NEXT_PUBLIC_GA_ID=G-XXXXXXXXXX

# Site URL
NEXT_PUBLIC_SITE_URL=https://pubuild.com
```

### 4. Domain & DNS Configuration

1. Purchase domain: `pubuild.com`
2. Configure DNS records:
   - A record pointing to hosting server IP
   - CNAME for www subdomain
   - MX records for email (if hosting email)
   - TXT record for SPF (email authentication)

3. SSL Certificate:
   - Most hosting providers (Vercel, Netlify) provide automatic SSL
   - For custom server: Use Let's Encrypt with Certbot

### 5. Testing

Run comprehensive tests before deployment:

```bash
# Build test
npm run build

# Type checking
npm run type-check

# Lint
npm run lint

# Local production test
npm run start
```

#### Lighthouse Audit

```bash
# Install Lighthouse CI
npm install -g @lhci/cli

# Run audit
lhci autorun --collect.url=http://localhost:3000

# Target scores: All ≥ 90
```

#### Accessibility Testing

1. Install axe DevTools extension
2. Test all major pages
3. Keyboard navigation test (Tab through all interactive elements)
4. Screen reader test (optional but recommended)

#### Cross-Browser Testing

Test on:
- Chrome/Edge (latest)
- Firefox (latest)
- Safari (desktop & iOS)
- Mobile Chrome (Android)

---

## Deployment Options

### Option 1: Vercel (Recommended - Easiest)

**Best for**: Quick deployment, automatic CI/CD

1. **Push to GitHub:**
   ```bash
   git init
   git add .
   git commit -m "Initial commit"
   git branch -M main
   git remote add origin https://github.com/yourusername/pubuild-website.git
   git push -u origin main
   ```

2. **Deploy to Vercel:**
   - Go to [vercel.com](https://vercel.com)
   - Click "Import Project"
   - Connect your GitHub repository
   - Configure:
     - Framework: Next.js
     - Root Directory: `./`
     - Build Command: `npm run build`
     - Output Directory: `.next`

3. **Configure Environment Variables:**
   - Go to Project Settings → Environment Variables
   - Add all variables from `.env.example`

4. **Configure Domain:**
   - Go to Project Settings → Domains
   - Add `pubuild.com` and `www.pubuild.com`
   - Update DNS as instructed

5. **Deploy:**
   - Vercel automatically deploys on every push to main
   - View deployment logs for any issues

**Cost**: Free tier available, paid plans start at $20/month

---

### Option 2: Netlify

**Best for**: Static hosting, easy setup

1. **Build for static export** (if using SSR features, skip this):
   Update `next.config.js`:
   ```javascript
   const nextConfig = {
     output: 'export',
     // ... rest of config
   };
   ```

2. **Deploy:**
   - Go to [netlify.com](https://netlify.com)
   - Drag and drop your `/out` folder after running `npm run build`
   - Or connect GitHub for automatic deployment

3. **Configure:**
   - Set build command: `npm run build`
   - Publish directory: `out` (or `.next` if not using export)
   - Add environment variables in Site settings

4. **Custom Domain:**
   - Go to Domain settings
   - Add custom domain `pubuild.com`
   - Configure DNS

**Cost**: Free tier available, paid plans start at $19/month

---

### Option 3: AWS (EC2 + CloudFront)

**Best for**: Full control, enterprise requirements

#### 3.1 Deploy to EC2

1. **Launch EC2 instance:**
   - Ubuntu Server 22.04 LTS
   - t3.medium or larger
   - Configure security group (ports 22, 80, 443)

2. **Connect and setup:**
   ```bash
   ssh -i your-key.pem ubuntu@your-ec2-ip

   # Install Node.js
   curl -fsSL https://deb.nodesource.com/setup_18.x | sudo -E bash -
   sudo apt-get install -y nodejs

   # Install PM2
   sudo npm install -g pm2

   # Clone repository
   git clone https://github.com/yourusername/pubuild-website.git
   cd pubuild-website

   # Install and build
   npm ci
   npm run build

   # Start with PM2
   pm2 start npm --name "pubuild-website" -- start
   pm2 save
   pm2 startup
   ```

3. **Configure Nginx reverse proxy:**
   ```bash
   sudo apt-get install nginx

   # Create config
   sudo nano /etc/nginx/sites-available/pubuild
   ```

   Add:
   ```nginx
   server {
       listen 80;
       server_name pubuild.com www.pubuild.com;

       location / {
           proxy_pass http://localhost:3000;
           proxy_http_version 1.1;
           proxy_set_header Upgrade $http_upgrade;
           proxy_set_header Connection 'upgrade';
           proxy_set_header Host $host;
           proxy_cache_bypass $http_upgrade;
       }
   }
   ```

   Enable:
   ```bash
   sudo ln -s /etc/nginx/sites-available/pubuild /etc/nginx/sites-enabled/
   sudo nginx -t
   sudo systemctl restart nginx
   ```

4. **Setup SSL with Let's Encrypt:**
   ```bash
   sudo apt-get install certbot python3-certbot-nginx
   sudo certbot --nginx -d pubuild.com -d www.pubuild.com
   ```

#### 3.2 CloudFront CDN (Optional)

1. Create CloudFront distribution
2. Point origin to EC2 instance
3. Configure caching rules
4. Update DNS to CloudFront distribution

**Cost**: EC2 ~$30-100/month + data transfer

---

### Option 4: Docker + Container Hosting

**Best for**: Kubernetes, containerized infrastructure

1. **Create Dockerfile:**

   Already provided in README, or use:
   ```dockerfile
   FROM node:18-alpine AS base

   FROM base AS deps
   WORKDIR /app
   COPY package*.json ./
   RUN npm ci

   FROM base AS builder
   WORKDIR /app
   COPY --from=deps /app/node_modules ./node_modules
   COPY . .
   RUN npm run build

   FROM base AS runner
   WORKDIR /app
   ENV NODE_ENV production
   RUN addgroup --system --gid 1001 nodejs
   RUN adduser --system --uid 1001 nextjs
   COPY --from=builder /app/public ./public
   COPY --from=builder --chown=nextjs:nodejs /app/.next/standalone ./
   COPY --from=builder --chown=nextjs:nodejs /app/.next/static ./.next/static
   USER nextjs
   EXPOSE 3000
   ENV PORT 3000
   CMD ["node", "server.js"]
   ```

2. **Build and run:**
   ```bash
   docker build -t pubuild-website .
   docker run -p 3000:3000 --env-file .env.production pubuild-website
   ```

3. **Deploy to:**
   - AWS ECS/Fargate
   - Google Cloud Run
   - Azure Container Instances
   - DigitalOcean App Platform
   - Render.com

**Cost**: Varies by platform, typically $10-50/month

---

### Option 5: Static Hosting (S3, Azure Storage)

**Only if SSR is not needed**

1. **Export static site:**
   ```bash
   # Update next.config.js: output: 'export'
   npm run build
   # Output in /out directory
   ```

2. **Deploy to AWS S3:**
   ```bash
   aws s3 sync out/ s3://pubuild.com/ --delete
   aws cloudfront create-invalidation --distribution-id YOUR_DIST_ID --paths "/*"
   ```

3. **Configure CloudFront** for SSL and CDN

**Cost**: Very low, typically <$5/month

---

## Post-Deployment Tasks

### 1. Verify Deployment

- [ ] Visit https://pubuild.com
- [ ] Test all pages load correctly
- [ ] Test forms (contact & RFP)
- [ ] Verify email delivery
- [ ] Test mobile responsiveness
- [ ] Check SSL certificate (should show padlock)

### 2. SEO & Analytics Setup

- [ ] Submit sitemap to Google Search Console
  - Go to https://search.google.com/search-console
  - Add property: pubuild.com
  - Submit sitemap: https://pubuild.com/sitemap.xml

- [ ] Set up Google Analytics (if using)
  - Create GA4 property
  - Add measurement ID to environment variables

- [ ] Verify robots.txt: https://pubuild.com/robots.txt

### 3. Monitoring & Maintenance

- [ ] Set up uptime monitoring (UptimeRobot, Pingdom)
- [ ] Configure error tracking (Sentry, optional)
- [ ] Set up backup strategy
- [ ] Document maintenance procedures
- [ ] Schedule regular content updates

### 4. Legal Compliance

- [ ] Review all legal pages with attorney if needed
- [ ] Ensure privacy policy matches actual practices
- [ ] Test cookie consent banner
- [ ] Verify GDPR/CCPA compliance if applicable

### 5. Performance Monitoring

- [ ] Run Lighthouse audit on production URL
- [ ] Check Core Web Vitals in Google Search Console
- [ ] Monitor page load times
- [ ] Set up performance budgets

---

## Troubleshooting

### Build Fails

```bash
# Clear cache and rebuild
npm run clean
rm -rf node_modules package-lock.json
npm install
npm run build
```

### Forms Not Working

1. Check email service configuration
2. Verify environment variables are set
3. Check server logs for errors
4. Test API routes directly: `/api/contact` and `/api/rfp`

### SSL Issues

- Ensure DNS propagation is complete (can take 24-48 hours)
- Verify certificate is installed correctly
- Check for mixed content warnings (HTTP resources on HTTPS page)

### Performance Issues

- Enable compression in hosting settings
- Verify CDN/CloudFront is configured
- Check for large images that need optimization
- Review Lighthouse report for specific issues

---

## Rollback Procedure

If deployment fails:

### Vercel/Netlify
- Go to Deployments
- Click on previous working deployment
- Click "Promote to Production"

### Manual Server
```bash
pm2 stop pubuild-website
git reset --hard HEAD~1
npm run build
pm2 restart pubuild-website
```

---

## Emergency Contacts

- **Hosting Support**: [Your hosting provider support]
- **Domain Registrar**: [Your registrar support]
- **Email Service**: [Your email provider support]
- **Developer Contact**: [Your contact information]

---

## Maintenance Schedule

**Daily:**
- Monitor uptime and error logs

**Weekly:**
- Review form submissions
- Check email delivery
- Review analytics

**Monthly:**
- Update dependencies: `npm update`
- Review security advisories
- Audit accessibility
- Backup database/content

**Quarterly:**
- Lighthouse audit
- Full security review
- Content review and updates
- SEO performance review

---

**Questions?** Contact sales@pubuild.com or (800) 474-1388
