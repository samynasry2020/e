export default function SolutionsLanding() {
  return (
    <section className="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-12">
      <h1 className="text-2xl font-bold">Solutions</h1>
      <ul className="mt-6 grid gap-3 sm:grid-cols-2">
        <li className="rounded border border-slate-200 p-4 bg-white">
          <h2 className="text-sm font-semibold">AI/HPC Infrastructure</h2>
          <p className="mt-2 text-sm text-slate-600">Validated GPU nodes, high-speed fabrics, power and cooling planning.</p>
        </li>
        <li className="rounded border border-slate-200 p-4 bg-white">
          <h2 className="text-sm font-semibold">Data Center Builds</h2>
          <p className="mt-2 text-sm text-slate-600">Racks, PDUs, structured cabling, KVMs, and deployment runbooks.</p>
        </li>
        <li className="rounded border border-slate-200 p-4 bg-white">
          <h2 className="text-sm font-semibold">Workstation Fleets</h2>
          <p className="mt-2 text-sm text-slate-600">CAD, media, and data science fleets with lifecycle support.</p>
        </li>
      </ul>
    </section>
  );
}
