# PUBUILD TECHNOLOGIES INC. — Static Website

This is a static website for `pubuild.com` serving U.S. Government agencies exclusively.

## Local preview

Use any static file server. For example with Python:

```bash
python3 -m http.server 8080 --directory .
```

Then visit `http://localhost:8080`.

## Structure

- `index.html` — Homepage
- `services.html` `capabilities.html` `contracts.html` `compliance.html`
- `about.html` `contact.html`
- `privacy.html` `terms.html` `accessibility.html` `security.html`
- `404.html` — Not found
- `assets/css/styles.css` — Global styles
- `assets/js/main.js` — Minimal enhancements
- `assets/images/logo.svg` — Logo
- `favicon.svg`, `robots.txt`, `sitemap.xml`

## Deployment

Host on any static provider (S3/CloudFront, Azure Static Web Apps, GitHub Pages, etc.). Ensure HTTPS and correct MIME types for `.svg`.