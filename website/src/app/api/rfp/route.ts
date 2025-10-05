import { NextRequest, NextResponse } from "next/server";
import rateLimit from "next-rate-limit";
import { siteConfig } from "@/lib/config";

const limiter = rateLimit({ interval: 60_000, uniqueTokenPerInterval: 500 });

export async function POST(req: NextRequest) {
  try {
    const headers = limiter.checkNext(req, 10);

    const form = await req.formData();
    const hp = form.get("_hp");
    if (typeof hp === "string" && hp.trim().length > 0) {
      return NextResponse.json({ ok: true });
    }

    const agency = String(form.get("agency") || "").slice(0, 200);
    const deadline = String(form.get("deadline") || "").slice(0, 100);
    const budgetRange = String(form.get("budgetRange") || "").slice(0, 200);
    const specs = String(form.get("specs") || "").slice(0, 20_000);
    const contactName = String(form.get("contactName") || "").slice(0, 200);
    const contactEmail = String(form.get("contactEmail") || "").slice(0, 200);
    const contactPhone = String(form.get("contactPhone") || "").slice(0, 50);

    if (!agency || !deadline || !specs || !contactName || !contactEmail) {
      return NextResponse.json({ ok: false, message: "Missing fields" }, { status: 400 });
    }

    const attachment = form.get("attachment");
    if (attachment && attachment instanceof File) {
      if (attachment.size > siteConfig.uploads.maxFileBytes) {
        return NextResponse.json({ ok: false, message: "File too large" }, { status: 400 });
      }
      const type = attachment.type;
      if (!siteConfig.uploads.allowedMimeTypes.includes(type)) {
        return NextResponse.json({ ok: false, message: "Unsupported file type" }, { status: 400 });
      }
      // In a real deployment, stream to object storage
    }

    // TODO: Add CAPTCHA verification if configured
    // TODO: Send email/make ticket

    return NextResponse.json({ ok: true });
  } catch (error) {
    return NextResponse.json({ ok: false, message: "Rate limit or server error" }, { status: 429 });
  }
}
