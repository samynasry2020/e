export async function GET() {
  const body = JSON.stringify({
    name: "PUBUILD",
    short_name: "PUBUILD",
    start_url: "/",
    display: "standalone",
    background_color: "#ffffff",
    theme_color: "#0E7490",
    icons: [],
  });
  return new Response(body, { headers: { "Content-Type": "application/manifest+json" } });
}
