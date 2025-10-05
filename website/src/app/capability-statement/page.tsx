export default function CapabilityStatementPage() {
  return (
    <section className="mx-auto max-w-3xl px-4 sm:px-6 lg:px-8 py-12">
      <h1 className="text-2xl font-bold">Capability Statement</h1>
      <p className="mt-2 text-slate-600">Download a one-page capability statement as PDF.</p>
      <a href="/capability-statement.pdf" className="btn-primary inline-flex items-center rounded px-4 py-2 mt-4">Download PDF</a>
    </section>
  );
}
