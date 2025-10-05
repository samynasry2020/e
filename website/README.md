PUBUILD website — Next.js (App Router), Tailwind CSS v4, TypeScript

Requirements met: WCAG 2.1 AA baseline, cookie consent, SEO meta/schema, sitemap/robots, security headers, contact/RFP forms with honeypot and rate limit, optional Arabic `/ar` routing.

Quick start

1. Install Node 18+ (Node 22 recommended)
2. Install deps: `npm install`
3. Run dev: `npm run dev`
4. Build: `npm run build` then `npm start`

Environment variables

- `NEXT_PUBLIC_GA_ID` — optional GA4 Measurement ID
- `NEXT_PUBLIC_COOKIE_BANNER_MODE` — `opt_in` (EU) or `opt_out` (US). Default: `opt_out`.

Content placeholders

- Update `src/lib/config.ts` with UEI, CAGE, NAICS, addresses, phone.

Compliance notes

- No third-party logos unless licensed. Use text “compatible with ...” with public links only.
- Government disclaimer is present in footer and government page.
- Export controls note included.

Lighthouse

- Start the app and run: `LH_URL=http://localhost:3000 npm run lighthouse`
