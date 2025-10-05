"use client";

import Link from "next/link";
import { useState } from "react";
import Image from "next/image";
import { siteConfig } from "@/lib/config";

export function Header() {
  const [open, setOpen] = useState<boolean>(false);

  return (
    <header className="border-b border-black/10 bg-white text-slate-900 sticky top-0 z-40">
      <a href="#main" className="sr-only focus:not-sr-only focus:absolute focus:top-2 focus:left-2 focus:bg-white focus:text-slate-900 focus:ring-2 focus:ring-[--color-primary] px-3 py-1 rounded">
        Skip to content
      </a>
      <div className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div className="flex h-16 items-center justify-between gap-6">
          <div className="flex items-center gap-3">
            <Link href="/" aria-label={`${siteConfig.siteName} home`} className="flex items-center gap-2">
              <Image src="/logo.svg" alt="PUBUILD logo" width={32} height={32} />
              <span className="font-semibold tracking-wide text-slate-900">{siteConfig.siteName}</span>
            </Link>
          </div>
          <nav className="hidden md:block" aria-label="Primary">
            <ul className="flex items-center gap-6 text-sm font-medium">
              <li><Link className="hover:text-[--color-primary]" href="/products">Products</Link></li>
              <li><Link className="hover:text-[--color-primary]" href="/solutions">Solutions</Link></li>
              <li><Link className="hover:text-[--color-primary]" href="/government">Government Contracting</Link></li>
              <li><Link className="hover:text-[--color-primary]" href="/resources">Resources</Link></li>
              <li><Link className="hover:text-[--color-primary]" href="/about">About</Link></li>
              <li><Link className="hover:text-[--color-primary]" href="/contact" aria-label="Contact sales">Contact</Link></li>
            </ul>
          </nav>
          <div className="hidden md:flex items-center gap-4">
            <a href={`tel:${siteConfig.contact.phone}`} className="text-sm font-mono hover:text-[--color-primary]" aria-label="Call sales">
              {formatPhone(siteConfig.contact.phone)}
            </a>
            <Link href="/contact" className="inline-flex items-center rounded bg-[--color-primary] px-3 py-2 text-white hover:opacity-90 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[--color-accent]">
              Get a Quote
            </Link>
          </div>
          <button
            type="button"
            className="md:hidden inline-flex items-center justify-center rounded p-2 text-slate-900 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-[--color-accent]"
            aria-expanded={open}
            aria-controls="mobile-nav"
            aria-label="Toggle navigation"
            onClick={() => setOpen((v) => !v)}
          >
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden>
              <path d="M4 7h16M4 12h16M4 17h16" stroke="currentColor" strokeWidth="2" strokeLinecap="round" />
            </svg>
          </button>
        </div>
      </div>
      <div id="mobile-nav" hidden={!open} className="md:hidden border-t border-black/10 bg-white">
        <nav className="mx-auto max-w-7xl px-4 py-3" aria-label="Mobile">
          <ul className="grid gap-3 text-sm">
            <li><Link className="block py-1" href="/products" onClick={() => setOpen(false)}>Products</Link></li>
            <li><Link className="block py-1" href="/solutions" onClick={() => setOpen(false)}>Solutions</Link></li>
            <li><Link className="block py-1" href="/government" onClick={() => setOpen(false)}>Government Contracting</Link></li>
            <li><Link className="block py-1" href="/resources" onClick={() => setOpen(false)}>Resources</Link></li>
            <li><Link className="block py-1" href="/about" onClick={() => setOpen(false)}>About</Link></li>
            <li><Link className="block py-1" href="/contact" onClick={() => setOpen(false)}>Contact</Link></li>
          </ul>
          <div className="mt-3 border-t border-black/10 pt-3">
            <a href={`tel:${siteConfig.contact.phone}`} className="block text-sm font-mono">{formatPhone(siteConfig.contact.phone)}</a>
          </div>
        </nav>
      </div>
    </header>
  );
}

function formatPhone(digits: string): string {
  const cleaned = digits.replace(/\D/g, "");
  const m = cleaned.match(/(\d{3})(\d{3})(\d{4})/);
  return m ? `(${m[1]}) ${m[2]}-${m[3]}` : digits;
}
