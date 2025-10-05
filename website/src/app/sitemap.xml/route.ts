import { siteConfig } from "@/lib/config";

export async function GET() {
  const urls = [
    "",
    "/products",
    "/solutions",
    "/government",
    "/about",
    "/resources",
    "/contact",
    "/legal/privacy-policy",
    "/legal/terms",
    "/legal/accessibility",
    "/legal/cookies",
  ];
  const now = new Date().toISOString();
  const entries = urls
    .map((u) => `<url><loc>${siteConfig.baseUrl}${u}</loc><lastmod>${now}</lastmod><changefreq>weekly</changefreq></url>`) 
    .join("");
  const xml = `<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">${entries}</urlset>`;
  return new Response(xml, { headers: { "Content-Type": "application/xml" } });
}
