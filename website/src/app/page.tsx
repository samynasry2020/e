import Link from "next/link";

export default function Home() {
  return (
    <section className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-16">
      <div className="grid gap-8 lg:grid-cols-2 lg:items-center">
        <div>
          <h1 className="text-3xl sm:text-4xl font-bold tracking-tight text-slate-900">
            IT hardware and government contracting, done right
          </h1>
          <p className="mt-4 text-slate-600 max-w-prose">
            Servers, workstations, rackmount, GPUs, storage, networking, and monitors—
            delivered with secure procurement and agency-ready documentation.
          </p>
          <div className="mt-6 flex flex-wrap gap-3">
            <Link href="/government" className="btn-primary inline-flex items-center rounded px-4 py-2">
              Government Contracting
            </Link>
            <Link href="/products" className="inline-flex items-center rounded border border-slate-300 px-4 py-2 hover:bg-slate-50">
              Explore Products
            </Link>
            <Link href="/contact" className="inline-flex items-center rounded border border-slate-300 px-4 py-2 hover:bg-slate-50">
              Contact Sales
            </Link>
          </div>
          <p className="mt-6 text-xs text-slate-500">
            We are an independent supplier. No government endorsement implied.
          </p>
        </div>
        <div className="rounded-lg border border-slate-200 p-6 bg-white">
          <h2 className="text-sm font-semibold">Top Categories</h2>
          <ul className="mt-3 grid grid-cols-2 gap-2 text-sm">
            <li><Link className="block rounded border border-slate-200 p-3 hover:border-[--color-primary]" href="/products/servers">Servers</Link></li>
            <li><Link className="block rounded border border-slate-200 p-3 hover:border-[--color-primary]" href="/products/rackmount">Rackmount</Link></li>
            <li><Link className="block rounded border border-slate-200 p-3 hover:border-[--color-primary]" href="/products/workstations">Workstations</Link></li>
            <li><Link className="block rounded border border-slate-200 p-3 hover:border-[--color-primary]" href="/products/gpus">GPUs</Link></li>
            <li><Link className="block rounded border border-slate-200 p-3 hover:border-[--color-primary]" href="/products/storage">Storage</Link></li>
            <li><Link className="block rounded border border-slate-200 p-3 hover:border-[--color-primary]" href="/products/networking">Networking</Link></li>
            <li><Link className="block rounded border border-slate-200 p-3 hover:border-[--color-primary]" href="/products/monitors">Monitors/Displays</Link></li>
            <li><Link className="block rounded border border-slate-200 p-3 hover:border-[--color-primary]" href="/products/accessories">Accessories</Link></li>
          </ul>
        </div>
      </div>
      <div className="mt-12 grid gap-6 sm:grid-cols-3">
        <div className="rounded-md border border-slate-200 p-4 bg-white">
          <h3 className="text-sm font-semibold">Privacy-first</h3>
          <p className="mt-2 text-sm text-slate-600">Cookie consent, IP anonymization, and opt-in analytics.</p>
        </div>
        <div className="rounded-md border border-slate-200 p-4 bg-white">
          <h3 className="text-sm font-semibold">WCAG 2.1 AA</h3>
          <p className="mt-2 text-sm text-slate-600">Semantic HTML, keyboard nav, contrast-checked styles.</p>
        </div>
        <div className="rounded-md border border-slate-200 p-4 bg-white">
          <h3 className="text-sm font-semibold">Fast by default</h3>
          <p className="mt-2 text-sm text-slate-600">Optimized images, lazy loading, code splitting, HTTP/2 caching.</p>
        </div>
      </div>
    </section>
  );
}
