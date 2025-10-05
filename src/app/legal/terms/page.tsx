import Link from "next/link";
import { ArrowLeft } from "lucide-react";

export const metadata = {
  title: "Terms of Use - Pubuild",
  description: "Terms of Use for Pubuild website and services.",
};

export default function TermsOfUsePage() {
  return (
    <div className="min-h-screen bg-gray-50">
      <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        {/* Back to previous page */}
        <div className="mb-8">
          <Link
            href="/"
            className="inline-flex items-center text-primary hover:underline"
          >
            <ArrowLeft className="mr-2 h-4 w-4" />
            Back to Home
          </Link>
        </div>

        <div className="bg-white rounded-lg shadow-sm p-8">
          <h1 className="text-3xl font-bold text-gray-900 mb-8">
            Terms of Use
          </h1>

          <div className="prose prose-lg max-w-none">
            <p className="text-gray-600 mb-8">
              <strong>Last Updated:</strong> {new Date().toLocaleDateString()}
            </p>

            <section className="mb-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-4">
                1. Acceptance of Terms
              </h2>
              <p className="text-gray-700 mb-4">
                By accessing and using pubuild.com ("the Website"), you accept and agree to be bound
                by the terms and provision of this agreement. These Terms of Use constitute the entire
                agreement between Pubuild and you concerning the use of the Website.
              </p>
            </section>

            <section className="mb-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-4">
                2. Use License
              </h2>
              <p className="text-gray-700 mb-4">
                Permission is granted to temporarily download one copy of the materials on the Website
                for personal, non-commercial transitory viewing only. This is the grant of a license,
                not a transfer of title, and under this license you may not:
              </p>
              <ul className="list-disc pl-6 text-gray-700 mb-4">
                <li>modify or copy the materials</li>
                <li>use the materials for any commercial purpose or for any public display</li>
                <li>attempt to reverse engineer any software contained on the Website</li>
                <li>remove any copyright or other proprietary notations from the materials</li>
              </ul>
            </section>

            <section className="mb-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-4">
                3. Disclaimer
              </h2>
              <p className="text-gray-700 mb-4">
                The materials on the Website are provided on an 'as is' basis. Pubuild makes no
                warranties, expressed or implied, and hereby disclaims and negates all other warranties
                including without limitation, implied warranties or conditions of merchantability,
                fitness for a particular purpose, or non-infringement of intellectual property or
                other violation of rights.
              </p>
            </section>

            <section className="mb-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-4">
                4. Limitations
              </h2>
              <p className="text-gray-700 mb-4">
                In no event shall Pubuild or its suppliers be liable for any damages (including,
                without limitation, damages for loss of data or profit, or due to business interruption)
                arising out of the use or inability to use the materials on the Website, even if Pubuild
                or an authorized representative has been notified orally or in writing of the possibility
                of such damage.
              </p>
            </section>

            <section className="mb-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-4">
                5. Accuracy of Materials
              </h2>
              <p className="text-gray-700 mb-4">
                The materials appearing on the Website could include technical, typographical, or
                photographic errors. Pubuild does not warrant that any of the materials on its Website
                are accurate, complete, or current. Pubuild may make changes to the materials contained
                on its Website at any time without notice.
              </p>
            </section>

            <section className="mb-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-4">
                6. Links
              </h2>
              <p className="text-gray-700 mb-4">
                Pubuild has not reviewed all of the sites linked to its Website and is not responsible
                for the contents of any such linked site. The inclusion of any link does not imply
                endorsement by Pubuild of the site. Use of any such linked website is at the user's own risk.
              </p>
            </section>

            <section className="mb-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-4">
                7. Modifications
              </h2>
              <p className="text-gray-700 mb-4">
                Pubuild may revise these Terms of Use for its Website at any time without notice.
                By using this Website, you are agreeing to be bound by the then current version of
                these Terms of Use.
              </p>
            </section>

            <section className="mb-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-4">
                8. Governing Law
              </h2>
              <p className="text-gray-700 mb-4">
                These terms and conditions are governed by and construed in accordance with the laws
                of California, and you irrevocably submit to the exclusive jurisdiction of the courts
                in that state or location.
              </p>
            </section>

            <div className="border-t pt-8 text-center">
              <p className="text-gray-500 text-sm">
                These Terms of Use are effective as of {new Date().toLocaleDateString()}.
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}