import Layout from '@/components/layout/layout'

export default function TermsPage() {
  return (
    <Layout>
      <div className="bg-white py-16">
        <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
          <h1 className="text-4xl font-bold text-gray-900 mb-8">
            Terms of Use
          </h1>
          
          <div className="prose prose-lg max-w-none">
            <p className="text-gray-600 mb-6">
              <strong>Last updated:</strong> {new Date().toLocaleDateString()}
            </p>

            <section className="mb-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-4">
                1. Acceptance of Terms
              </h2>
              <p className="text-gray-700 mb-4">
                By accessing and using this website, you accept and agree to be bound by the terms 
                and provision of this agreement. If you do not agree to abide by the above, please 
                do not use this service.
              </p>
            </section>

            <section className="mb-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-4">
                2. Use License
              </h2>
              <p className="text-gray-700 mb-4">
                Permission is granted to temporarily download one copy of the materials on Pubuild's 
                website for personal, non-commercial transitory viewing only. This is the grant of a 
                license, not a transfer of title, and under this license you may not:
              </p>
              <ul className="list-disc pl-6 text-gray-700">
                <li>Modify or copy the materials</li>
                <li>Use the materials for any commercial purpose or for any public display</li>
                <li>Attempt to reverse engineer any software contained on the website</li>
                <li>Remove any copyright or other proprietary notations from the materials</li>
              </ul>
            </section>

            <section className="mb-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-4">
                3. Disclaimer
              </h2>
              <p className="text-gray-700 mb-4">
                The materials on Pubuild's website are provided on an 'as is' basis. Pubuild makes no 
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
                In no event shall Pubuild or its suppliers be liable for any damages (including, without 
                limitation, damages for loss of data or profit, or due to business interruption) arising 
                out of the use or inability to use the materials on Pubuild's website, even if Pubuild 
                or a Pubuild authorized representative has been notified orally or in writing of the 
                possibility of such damage. Because some jurisdictions do not allow limitations on implied 
                warranties, or limitations of liability for consequential or incidental damages, these 
                limitations may not apply to you.
              </p>
            </section>

            <section className="mb-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-4">
                5. Accuracy of Materials
              </h2>
              <p className="text-gray-700 mb-4">
                The materials appearing on Pubuild's website could include technical, typographical, or 
                photographic errors. Pubuild does not warrant that any of the materials on its website 
                are accurate, complete, or current. Pubuild may make changes to the materials contained 
                on its website at any time without notice.
              </p>
            </section>

            <section className="mb-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-4">
                6. Links
              </h2>
              <p className="text-gray-700 mb-4">
                Pubuild has not reviewed all of the sites linked to our website and is not responsible 
                for the contents of any such linked site. The inclusion of any link does not imply 
                endorsement by Pubuild of the site. Use of any such linked website is at the user's 
                own risk.
              </p>
            </section>

            <section className="mb-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-4">
                7. Modifications
              </h2>
              <p className="text-gray-700 mb-4">
                Pubuild may revise these terms of service for its website at any time without notice. 
                By using this website you are agreeing to be bound by the then current version of these 
                terms of service.
              </p>
            </section>

            <section className="mb-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-4">
                8. Governing Law
              </h2>
              <p className="text-gray-700 mb-4">
                These terms and conditions are governed by and construed in accordance with the laws of 
                California, USA and you irrevocably submit to the exclusive jurisdiction of the courts 
                in that state or location.
              </p>
            </section>

            <section className="mb-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-4">
                9. Intellectual Property
              </h2>
              <p className="text-gray-700 mb-4">
                All content, trademarks, and other intellectual property on this website are owned by 
                Pubuild or its licensors. You may not use any of our intellectual property without 
                our prior written consent.
              </p>
            </section>

            <section className="mb-8">
              <h2 className="text-2xl font-semibold text-gray-900 mb-4">
                10. Contact Information
              </h2>
              <p className="text-gray-700 mb-4">
                If you have any questions about these Terms of Use, please contact us:
              </p>
              <div className="bg-gray-50 rounded-lg p-6">
                <p className="text-gray-700 mb-2">
                  <strong>Email:</strong> legal@pubuild.com
                </p>
                <p className="text-gray-700 mb-2">
                  <strong>Phone:</strong> 800-474-1388
                </p>
                <p className="text-gray-700">
                  <strong>Address:</strong> California, USA
                </p>
              </div>
            </section>
          </div>
        </div>
      </div>
    </Layout>
  )
}