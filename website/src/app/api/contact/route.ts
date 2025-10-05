import { NextRequest, NextResponse } from "next/server";
import rateLimit from "next-rate-limit";

const limiter = rateLimit({ interval: 60_000, uniqueTokenPerInterval: 500 });

export async function POST(req: NextRequest) {
  try {
    const headers = limiter.checkNext(req, 10);

    const form = await req.formData();
    const hp = form.get("_hp");
    if (typeof hp === "string" && hp.trim().length > 0) {
      return NextResponse.json({ ok: true });
    }

    const fullName = String(form.get("fullName") || "").slice(0, 200);
    const email = String(form.get("email") || "").slice(0, 200);
    const company = String(form.get("company") || "").slice(0, 200);
    const phone = String(form.get("phone") || "").slice(0, 50);
    const message = String(form.get("message") || "").slice(0, 10_000);

    if (!fullName || !email || !company || !message) {
      return NextResponse.json({ ok: false, message: "Missing fields" }, { status: 400 });
    }

    // TODO: Add CAPTCHA verification if NEXT_PUBLIC_CAPTCHA is configured
    // TODO: Send email via transactional provider or SMTP relay

    return NextResponse.json({ ok: true });
  } catch (error) {
    return NextResponse.json({ ok: false, message: "Rate limit or server error" }, { status: 429 });
  }
}
