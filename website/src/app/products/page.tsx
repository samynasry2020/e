import Link from "next/link";

const categories = [
  { slug: "servers", name: "Servers" },
  { slug: "rackmount", name: "Rackmount" },
  { slug: "workstations", name: "Workstations" },
  { slug: "gpus", name: "GPUs" },
  { slug: "storage", name: "Storage" },
  { slug: "networking", name: "Networking" },
  { slug: "monitors", name: "Monitors/Displays" },
  { slug: "accessories", name: "Accessories" },
];

export default function ProductsLanding() {
  return (
    <section className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
      <h1 className="text-2xl font-bold">Products</h1>
      <p className="mt-2 text-slate-600 max-w-prose">
        Explore enterprise hardware. Compatibility notes and procurement-ready details are provided on each product page.
      </p>
      <ul className="mt-6 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
        {categories.map((c) => (
          <li key={c.slug}>
            <Link href={`/products/${c.slug}`} className="block rounded border border-slate-200 p-4 hover:border-[--color-primary]">
              <span className="font-medium">{c.name}</span>
            </Link>
          </li>
        ))}
      </ul>
    </section>
  );
}
