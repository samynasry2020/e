export async function GET() {
  const body = `User-agent: *
Disallow:
Sitemap: https://pubuild.com/sitemap.xml`;
  return new Response(body, { headers: { "Content-Type": "text/plain" } });
}
