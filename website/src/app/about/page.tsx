import { siteConfig } from "@/lib/config";

export default function AboutPage() {
  return (
    <section className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
      <h1 className="text-2xl font-bold">About</h1>
      <p className="mt-2 text-slate-600 max-w-prose">
        Our mission is to deliver reliable, compatible IT hardware and solutions with clear, compliant procurement for public sector buyers.
      </p>
      <div className="mt-6 grid gap-4 sm:grid-cols-2">
        <div className="rounded border border-slate-200 p-4 bg-white">
          <h2 className="text-sm font-semibold">Locations</h2>
          <address className="mt-2 not-italic text-sm text-slate-600">
            {siteConfig.contact.addressLines.join(" · ")}
          </address>
        </div>
        <div className="rounded border border-slate-200 p-4 bg-white">
          <h2 className="text-sm font-semibold">Contact</h2>
          <p className="mt-2 text-sm text-slate-600">Email: {siteConfig.contact.salesEmail}</p>
          <p className="text-sm text-slate-600">Phone: {siteConfig.contact.phone}</p>
        </div>
      </div>
    </section>
  );
}
