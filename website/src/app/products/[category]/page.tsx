import { notFound } from "next/navigation";
import Link from "next/link";

const allowed = new Set([
  "servers",
  "rackmount",
  "workstations",
  "gpus",
  "storage",
  "networking",
  "monitors",
  "accessories",
]);

export default async function CategoryPage({ params }: { params: Promise<{ category: string }> }) {
  const resolved = await params;
  const category = resolved.category;
  if (!allowed.has(category)) return notFound();

  return (
    <section className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
      <nav aria-label="Breadcrumb" className="text-sm text-slate-500">
        <ol className="flex gap-2">
          <li><Link className="underline" href="/products">Products</Link></li>
          <li aria-hidden>/</li>
          <li aria-current="page" className="capitalize">{category}</li>
        </ol>
      </nav>
      <h1 className="mt-2 text-2xl font-bold capitalize">{category}</h1>
      <p className="mt-2 text-slate-600 max-w-prose">
        Brief specs and compatibility notes. Contact us for validated configurations and agency requirements.
      </p>
      <div className="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        {[1,2,3].map((n) => (
          <article key={n} className="rounded border border-slate-200 p-4 bg-white">
            <h2 className="text-sm font-semibold">{category} Model {n}</h2>
            <ul className="mt-2 text-sm list-disc pl-5 text-slate-600">
              <li>Placeholder spec line</li>
              <li>Compatibility: vendor public pages linked only</li>
            </ul>
            <div className="mt-3 flex gap-2">
              <Link href="/contact" className="btn-primary inline-flex items-center rounded px-3 py-1.5 text-sm">Request quote</Link>
              <Link href="/government" className="inline-flex items-center rounded border border-slate-300 px-3 py-1.5 text-sm hover:bg-slate-50">Gov procurement</Link>
            </div>
          </article>
        ))}
      </div>
    </section>
  );
}
