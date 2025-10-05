"use client";

import { useState } from "react";
import { HCaptcha } from "@/components/HCaptcha";
import { siteConfig } from "@/lib/config";

export default function RfpPage() {
  const [status, setStatus] = useState<string>("");

  async function onSubmit(event: React.FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setStatus("Submitting…");
    const data = new FormData(event.currentTarget);
    const res = await fetch("/api/rfp", { method: "POST", body: data });
    const json = await res.json();
    setStatus(json.ok ? "RFP received — we’ll respond within 1 business day." : json.message || "Error");
    if (json.ok) event.currentTarget.reset();
  }

  return (
    <section className="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 py-12">
      <h1 className="text-2xl font-bold">Bid Support (RFP Intake)</h1>
      <form className="mt-6 grid gap-4" onSubmit={onSubmit}>
        <input type="text" name="agency" placeholder="Agency" required aria-label="Agency" className="rounded border border-slate-300 p-2" />
        <input type="date" name="deadline" required aria-label="Deadline" className="rounded border border-slate-300 p-2" />
        <input type="text" name="budgetRange" placeholder="Budget range" aria-label="Budget range" className="rounded border border-slate-300 p-2" />
        <textarea name="specs" placeholder="Specifications" required aria-label="Specifications" className="rounded border border-slate-300 p-2 min-h-32" />
        <input type="file" name="attachment" aria-label="Attachment" className="rounded border border-slate-300 p-2" />
        <input type="text" name="contactName" placeholder="Contact name" required aria-label="Contact name" className="rounded border border-slate-300 p-2" />
        <input type="email" name="contactEmail" placeholder="Contact email" required aria-label="Contact email" className="rounded border border-slate-300 p-2" />
        <input type="tel" name="contactPhone" placeholder="Contact phone (optional)" aria-label="Contact phone" className="rounded border border-slate-300 p-2" />
        <input type="text" name="_hp" tabIndex={-1} autoComplete="off" className="hidden" aria-hidden />
        {siteConfig.captcha.hcaptchaSiteKey && <HCaptcha />}
        <button className="btn-primary inline-flex items-center rounded px-4 py-2" type="submit">Submit RFP</button>
      </form>
      {status && <p className="mt-3 text-sm text-slate-600" role="status">{status}</p>}
    </section>
  );
}
