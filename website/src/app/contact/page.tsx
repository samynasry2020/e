"use client";

import { useState } from "react";
import { HCaptcha } from "@/components/HCaptcha";
import { siteConfig } from "@/lib/config";

export default function ContactPage() {
  const [status, setStatus] = useState<string>("");

  async function onSubmit(event: React.FormEvent<HTMLFormElement>) {
    event.preventDefault();
    setStatus("Sending…");
    const form = event.currentTarget;
    const data = new FormData(form);
    const res = await fetch("/api/contact", { method: "POST", body: data });
    const json = await res.json();
    setStatus(json.ok ? "Thanks — we’ll be in touch." : json.message || "Error");
    if (json.ok) form.reset();
  }

  return (
    <section className="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 py-12">
      <h1 className="text-2xl font-bold">Contact</h1>
      <form className="mt-6 grid gap-4" onSubmit={onSubmit}>
        <input type="text" name="fullName" placeholder="Full name" required aria-label="Full name" className="rounded border border-slate-300 p-2" />
        <input type="email" name="email" placeholder="Email" required aria-label="Email" className="rounded border border-slate-300 p-2" />
        <input type="tel" name="phone" placeholder="Phone (optional)" aria-label="Phone" className="rounded border border-slate-300 p-2" />
        <input type="text" name="company" placeholder="Company" required aria-label="Company" className="rounded border border-slate-300 p-2" />
        <input type="text" name="_hp" tabIndex={-1} autoComplete="off" className="hidden" aria-hidden />
        <textarea name="message" placeholder="Message" required aria-label="Message" className="rounded border border-slate-300 p-2 min-h-32" />
        {siteConfig.captcha.hcaptchaSiteKey && <HCaptcha />}
        <button className="btn-primary inline-flex items-center rounded px-4 py-2" type="submit">Send</button>
      </form>
      {status && <p className="mt-3 text-sm text-slate-600" role="status">{status}</p>}
    </section>
  );
}
