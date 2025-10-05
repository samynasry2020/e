import { siteConfig } from "@/lib/config";
import Link from "next/link";

export default function GovernmentPage() {
  const { government } = siteConfig;
  return (
    <section className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
      <h1 className="text-2xl font-bold">Government Contracting</h1>
      <p className="mt-2 text-slate-600 max-w-prose">
        We support federal, state, and local agencies with compliant procurement of IT hardware and related services.
      </p>

      <div className="mt-6 grid gap-6 sm:grid-cols-2">
        <div className="rounded border border-slate-200 p-4 bg-white">
          <h2 className="text-sm font-semibold">Overview</h2>
          <ul className="mt-2 list-disc pl-5 text-sm text-slate-600">
            <li>Independent supplier — no government endorsement implied.</li>
            <li>OEM references via public product pages only; no unlicensed logos.</li>
            <li>Export control notice included on all pages.</li>
          </ul>
        </div>
        <div className="rounded border border-slate-200 p-4 bg-white">
          <h2 className="text-sm font-semibold">NAICS & Identifiers</h2>
          <p className="mt-2 text-sm">UEI: {government.uei} · CAGE: {government.cage}</p>
          <p className="mt-1 text-sm">NAICS: {government.naics.join(", ")}</p>
          <p className="mt-1 text-sm">PSC families: {government.pscFamilies.join(", ")}</p>
        </div>
      </div>

      {government.certifications.length > 0 && (
        <div className="mt-6 rounded border border-slate-200 p-4 bg-white">
          <h2 className="text-sm font-semibold">Set-aside Certifications</h2>
          <ul className="mt-2 list-disc pl-5 text-sm text-slate-600">
            {government.certifications.map((c) => (
              <li key={c}>{c}</li>
            ))}
          </ul>
        </div>
      )}

      <div className="mt-8 flex gap-3">
        <Link href="/government/rfp" className="btn-primary inline-flex items-center rounded px-4 py-2">Submit RFP</Link>
        <Link href="/contact" className="inline-flex items-center rounded border border-slate-300 px-4 py-2 hover:bg-slate-50">Contact Sales</Link>
      </div>

      <p className="mt-6 text-xs text-slate-500">We are an independent supplier. No government endorsement implied.</p>
    </section>
  );
}
