import Link from "next/link";
import Image from "next/image";
import { siteConfig } from "@/lib/config";

export function Footer() {
  return (
    <footer className="border-t border-black/10 bg-white text-slate-900" role="contentinfo">
      <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10 grid gap-8 sm:grid-cols-3">
        <div>
          <div className="flex items-center gap-2 mb-3">
            <Image src="/logo.svg" alt="PUBUILD logo" width={28} height={28} />
            <span className="font-semibold">{siteConfig.siteName}</span>
          </div>
          <p className="text-sm text-slate-600 max-w-prose">
            Enterprise IT hardware and solutions: servers, workstations, rackmount, GPUs, storage, networking, and monitors.
          </p>
        </div>
        <div>
          <h2 className="text-sm font-semibold mb-3">Company</h2>
          <ul className="space-y-2 text-sm">
            <li><Link className="hover:text-[--color-primary]" href="/about">About</Link></li>
            <li><Link className="hover:text-[--color-primary]" href="/resources">Resources</Link></li>
            <li><Link className="hover:text-[--color-primary]" href="/government">Government Contracting</Link></li>
            <li><Link className="hover:text-[--color-primary]" href="/contact">Contact</Link></li>
          </ul>
        </div>
        <div>
          <h2 className="text-sm font-semibold mb-3">Legal</h2>
          <ul className="space-y-2 text-sm">
            <li><Link className="hover:text-[--color-primary]" href="/legal/privacy-policy">Privacy Policy</Link></li>
            <li><Link className="hover:text-[--color-primary]" href="/legal/terms">Terms of Use</Link></li>
            <li><Link className="hover:text-[--color-primary]" href="/legal/accessibility">Accessibility Statement</Link></li>
            <li><Link className="hover:text-[--color-primary]" href="/legal/cookies">Cookie Policy</Link></li>
          </ul>
        </div>
      </div>
      <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 pb-10 flex flex-col gap-2 text-xs text-slate-600">
        <address className="not-italic">
          {siteConfig.contact.addressLines.join(" · ")}
        </address>
        <p>Sales: <a className="underline" href={`mailto:${siteConfig.contact.salesEmail}`}>{siteConfig.contact.salesEmail}</a> · Procurement: <a className="underline" href={`mailto:${siteConfig.contact.procurementEmail}`}>{siteConfig.contact.procurementEmail}</a> · Phone: <a className="underline" href={`tel:${siteConfig.contact.phone}`}>{formatPhone(siteConfig.contact.phone)}</a></p>
        <p>Office hours: {siteConfig.contact.officeHours} · SLA: {siteConfig.contact.responseSla}</p>
        <p className="mt-2">We are an independent supplier. No government endorsement implied.</p>
        <p>Products may be subject to U.S. export controls; purchaser is responsible for compliance.</p>
        <p className="mt-4">© {new Date().getFullYear()} {siteConfig.siteName}. All rights reserved.</p>
      </div>
    </footer>
  );
}

function formatPhone(digits: string): string {
  const cleaned = digits.replace(/\D/g, "");
  const m = cleaned.match(/(\d{3})(\d{3})(\d{4})/);
  return m ? `(${m[1]}) ${m[2]}-${m[3]}` : digits;
}
