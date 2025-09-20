# PUBUILD TECHNOLOGIES INC. Website

Static site for `pubuild.com` targeting U.S. government agencies.

## Run locally

Use any static server (Python, Node, or your IDE).

```bash
# Python 3
python3 -m http.server 8080 --directory /workspace/pubuild
# Then open http://localhost:8080/
```

## Structure

- `/index.html` — Home
- `/services.html` — Services
- `/contracts.html` — Contracts & Bids
- `/compliance.html` — Compliance
- `/about.html` — About
- `/contact.html` — Contact (.gov/.mil validation)
- `/assets/css/styles.css` — Styles
- `/assets/js/main.js` — Small JS helpers
- `/sitemap.xml`, `/robots.txt`, `/404.html`

## Notes

- Government-only supplier; forms enforce `.gov`/`.mil` emails client-side
- Add backend email or ticketing integration before production
- Update UEI/CAGE when available

## Deploy

- Any static hosting (S3+CloudFront, Netlify, Vercel, GitHub Pages). Point `pubuild.com` and `www.pubuild.com` via DNS.