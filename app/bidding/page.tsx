import Link from 'next/link'
import { 
  FileText, 
  Calendar, 
  DollarSign, 
  Building2, 
  Clock,
  Award,
  CheckCircle,
  ArrowRight,
  Download,
  Eye,
  Search,
  Filter,
  AlertCircle
} from 'lucide-react'

export default function BiddingPage() {
  const activeBids = [
    {
      id: "RFP-2025-001",
      title: "High-Performance Computing Servers for Defense Research",
      agency: "Department of Defense",
      type: "Request for Proposal",
      deadline: "March 15, 2025",
      value: "$2.5M - $5.0M",
      status: "Active",
      daysLeft: 23,
      description: "Procurement of 50 high-performance computing servers for advanced research operations.",
      requirements: ["Top Secret clearance required", "FIPS 140-2 Level 4", "24/7 support"]
    },
    {
      id: "IFB-2025-002",
      title: "Secure Server Infrastructure Upgrade",
      agency: "Department of Homeland Security",
      type: "Invitation for Bid",
      deadline: "February 28, 2025",
      value: "$1.8M - $3.2M",
      status: "Active",
      daysLeft: 8,
      description: "Complete server infrastructure upgrade for critical security operations.",
      requirements: ["FedRAMP High certification", "Secret clearance", "On-site installation"]
    },
    {
      id: "RFQ-2025-003",
      title: "Emergency Response Server Systems",
      agency: "Federal Emergency Management Agency",
      type: "Request for Quote",
      deadline: "March 30, 2025",
      value: "$800K - $1.5M",
      status: "Active",
      daysLeft: 38,
      description: "Rapid deployment server systems for emergency response coordination.",
      requirements: ["Mobile deployment capability", "Ruggedized design", "Satellite connectivity"]
    },
    {
      id: "RFP-2025-004",
      title: "Intelligence Data Processing Servers",
      agency: "Central Intelligence Agency",
      type: "Request for Proposal",
      deadline: "April 10, 2025",
      value: "$3.0M - $6.0M",
      status: "Pre-Solicitation",
      daysLeft: 49,
      description: "Advanced server systems for intelligence data processing and analysis.",
      requirements: ["TS/SCI clearance required", "SCIF installation", "Custom encryption"]
    }
  ]

  const pastWins = [
    {
      id: "RFP-2024-045",
      title: "FBI Field Office Server Deployment",
      agency: "Federal Bureau of Investigation",
      value: "$4.2M",
      year: "2024",
      status: "Completed"
    },
    {
      id: "IFB-2024-032",
      title: "NSA Secure Computing Infrastructure",
      agency: "National Security Agency",
      value: "$7.8M",
      year: "2024",
      status: "Completed"
    },
    {
      id: "RFQ-2024-018",
      title: "Border Security Server Systems",
      agency: "U.S. Customs and Border Protection",
      value: "$2.1M",
      year: "2024",
      status: "Completed"
    }
  ]

  const upcomingOpportunities = [
    {
      title: "Next-Generation Server Architecture",
      agency: "Department of Defense",
      estimatedValue: "$10M+",
      expectedRelease: "Q2 2025",
      type: "RFP"
    },
    {
      title: "Cloud-Edge Server Integration",
      agency: "Department of Veterans Affairs",
      estimatedValue: "$5M+",
      expectedRelease: "Q3 2025",
      type: "RFP"
    },
    {
      title: "Disaster Recovery Server Systems",
      agency: "Department of Homeland Security",
      estimatedValue: "$3M+",
      expectedRelease: "Q4 2025",
      type: "IFB"
    }
  ]

  const getStatusColor = (status: string) => {
    switch (status) {
      case 'Active':
        return 'bg-green-100 text-green-800'
      case 'Pre-Solicitation':
        return 'bg-blue-100 text-blue-800'
      case 'Completed':
        return 'bg-gray-100 text-gray-800'
      default:
        return 'bg-gray-100 text-gray-800'
    }
  }

  const getUrgencyColor = (daysLeft: number) => {
    if (daysLeft <= 7) return 'text-red-600'
    if (daysLeft <= 14) return 'text-yellow-600'
    return 'text-green-600'
  }

  return (
    <div className="min-h-screen">
      {/* Hero Section */}
      <section className="bg-gradient-to-r from-primary-600 to-primary-800 text-white py-20">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center">
            <h1 className="text-4xl md:text-5xl font-bold mb-6">
              Government Bidding Portal
            </h1>
            <p className="text-xl md:text-2xl mb-8 max-w-4xl mx-auto opacity-90">
              Active government bids, proposals, and contracting opportunities 
              for server solutions and technology services.
            </p>
            <div className="flex flex-col sm:flex-row gap-4 justify-center">
              <div className="flex items-center space-x-2 bg-white/10 px-4 py-2 rounded-lg">
                <Award className="h-5 w-5" />
                <span>GSA Schedule Holder</span>
              </div>
              <div className="flex items-center space-x-2 bg-white/10 px-4 py-2 rounded-lg">
                <CheckCircle className="h-5 w-5" />
                <span>Pre-Approved Vendor</span>
              </div>
              <div className="flex items-center space-x-2 bg-white/10 px-4 py-2 rounded-lg">
                <Building2 className="h-5 w-5" />
                <span>Multiple Contract Vehicles</span>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Search and Filter Section */}
      <section className="py-8 bg-white border-b">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex flex-col md:flex-row gap-4 items-center justify-between">
            <div className="flex-1 max-w-md">
              <div className="relative">
                <Search className="absolute left-3 top-1/2 transform -translate-y-1/2 h-5 w-5 text-secondary-400" />
                <input
                  type="text"
                  placeholder="Search opportunities..."
                  className="w-full pl-10 pr-4 py-2 border border-secondary-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent"
                />
              </div>
            </div>
            <div className="flex gap-4">
              <select className="px-4 py-2 border border-secondary-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                <option>All Agencies</option>
                <option>Department of Defense</option>
                <option>Department of Homeland Security</option>
                <option>Intelligence Agencies</option>
              </select>
              <select className="px-4 py-2 border border-secondary-300 rounded-lg focus:ring-2 focus:ring-primary-500 focus:border-transparent">
                <option>All Types</option>
                <option>RFP</option>
                <option>IFB</option>
                <option>RFQ</option>
              </select>
            </div>
          </div>
        </div>
      </section>

      {/* Active Bids Section */}
      <section className="py-20 bg-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="flex items-center justify-between mb-12">
            <div>
              <h2 className="text-3xl md:text-4xl font-bold text-secondary-900 mb-4">
                Active Bidding Opportunities
              </h2>
              <p className="text-xl text-secondary-600">
                Current government contracts and proposals available for bidding
              </p>
            </div>
            <div className="text-right">
              <div className="text-2xl font-bold text-primary-600">{activeBids.length}</div>
              <div className="text-secondary-600">Active Bids</div>
            </div>
          </div>

          <div className="space-y-6">
            {activeBids.map((bid, index) => (
              <div key={index} className="bg-white border border-secondary-200 rounded-lg p-6 shadow-sm hover:shadow-md transition-shadow">
                <div className="flex flex-col lg:flex-row lg:items-start lg:justify-between gap-4">
                  <div className="flex-1">
                    <div className="flex items-start justify-between mb-4">
                      <div>
                        <div className="flex items-center space-x-3 mb-2">
                          <h3 className="text-xl font-bold text-secondary-900">
                            {bid.title}
                          </h3>
                          <span className={`px-2 py-1 rounded-full text-xs font-medium ${getStatusColor(bid.status)}`}>
                            {bid.status}
                          </span>
                        </div>
                        <p className="text-secondary-600 text-sm mb-2">
                          {bid.agency} • {bid.type} • {bid.id}
                        </p>
                        <p className="text-secondary-700 mb-4">
                          {bid.description}
                        </p>
                      </div>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                      <div className="flex items-center space-x-2">
                        <Calendar className="h-4 w-4 text-secondary-400" />
                        <div>
                          <div className="text-sm font-medium text-secondary-900">Deadline</div>
                          <div className="text-sm text-secondary-600">{bid.deadline}</div>
                        </div>
                      </div>
                      <div className="flex items-center space-x-2">
                        <DollarSign className="h-4 w-4 text-secondary-400" />
                        <div>
                          <div className="text-sm font-medium text-secondary-900">Estimated Value</div>
                          <div className="text-sm text-secondary-600">{bid.value}</div>
                        </div>
                      </div>
                      <div className="flex items-center space-x-2">
                        <Clock className="h-4 w-4 text-secondary-400" />
                        <div>
                          <div className="text-sm font-medium text-secondary-900">Time Remaining</div>
                          <div className={`text-sm font-medium ${getUrgencyColor(bid.daysLeft)}`}>
                            {bid.daysLeft} days
                          </div>
                        </div>
                      </div>
                    </div>

                    <div className="mb-4">
                      <h4 className="text-sm font-medium text-secondary-900 mb-2">Key Requirements:</h4>
                      <div className="flex flex-wrap gap-2">
                        {bid.requirements.map((req, idx) => (
                          <span key={idx} className="bg-secondary-100 text-secondary-700 px-2 py-1 rounded text-xs">
                            {req}
                          </span>
                        ))}
                      </div>
                    </div>
                  </div>

                  <div className="flex flex-col space-y-2 lg:ml-6">
                    <button className="flex items-center px-4 py-2 bg-primary-600 text-white font-medium rounded-lg hover:bg-primary-700 transition-colors">
                      <Eye className="h-4 w-4 mr-2" />
                      View Details
                    </button>
                    <button className="flex items-center px-4 py-2 border border-primary-600 text-primary-600 font-medium rounded-lg hover:bg-primary-50 transition-colors">
                      <Download className="h-4 w-4 mr-2" />
                      Download RFP
                    </button>
                  </div>
                </div>

                {bid.daysLeft <= 7 && (
                  <div className="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                    <div className="flex items-center space-x-2">
                      <AlertCircle className="h-5 w-5 text-red-600" />
                      <span className="text-red-800 font-medium">Urgent: Deadline approaching in {bid.daysLeft} days</span>
                    </div>
                  </div>
                )}
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Past Wins Section */}
      <section className="py-20 bg-secondary-50">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-12">
            <h2 className="text-3xl md:text-4xl font-bold text-secondary-900 mb-4">
              Recent Contract Wins
            </h2>
            <p className="text-xl text-secondary-600 max-w-3xl mx-auto">
              Successfully completed government contracts demonstrating our expertise and reliability
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            {pastWins.map((win, index) => (
              <div key={index} className="bg-white p-6 rounded-lg shadow-md">
                <div className="flex items-start justify-between mb-4">
                  <div className="text-2xl font-bold text-green-600">
                    {win.value}
                  </div>
                  <span className="bg-green-100 text-green-800 px-2 py-1 rounded-full text-xs font-medium">
                    {win.status}
                  </span>
                </div>
                <h3 className="text-lg font-semibold text-secondary-900 mb-2">
                  {win.title}
                </h3>
                <p className="text-secondary-600 text-sm mb-2">
                  {win.agency}
                </p>
                <p className="text-secondary-500 text-sm">
                  Contract ID: {win.id} • {win.year}
                </p>
              </div>
            ))}
          </div>

          <div className="text-center mt-12">
            <div className="inline-flex items-center space-x-8 bg-white px-8 py-4 rounded-lg shadow-md">
              <div className="text-center">
                <div className="text-2xl font-bold text-primary-600">$14.1M</div>
                <div className="text-secondary-600 text-sm">Total Contract Value (2024)</div>
              </div>
              <div className="w-px h-8 bg-secondary-200"></div>
              <div className="text-center">
                <div className="text-2xl font-bold text-primary-600">12</div>
                <div className="text-secondary-600 text-sm">Contracts Won (2024)</div>
              </div>
              <div className="w-px h-8 bg-secondary-200"></div>
              <div className="text-center">
                <div className="text-2xl font-bold text-primary-600">100%</div>
                <div className="text-secondary-600 text-sm">On-Time Delivery</div>
              </div>
            </div>
          </div>
        </div>
      </section>

      {/* Upcoming Opportunities */}
      <section className="py-20 bg-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div className="text-center mb-12">
            <h2 className="text-3xl md:text-4xl font-bold text-secondary-900 mb-4">
              Upcoming Opportunities
            </h2>
            <p className="text-xl text-secondary-600 max-w-3xl mx-auto">
              Future government contracting opportunities in the pipeline
            </p>
          </div>

          <div className="grid grid-cols-1 md:grid-cols-3 gap-6">
            {upcomingOpportunities.map((opportunity, index) => (
              <div key={index} className="bg-gradient-to-br from-primary-50 to-primary-100 p-6 rounded-lg border border-primary-200">
                <div className="flex items-center justify-between mb-4">
                  <span className="bg-primary-600 text-white px-3 py-1 rounded-full text-xs font-medium">
                    {opportunity.type}
                  </span>
                  <span className="text-primary-700 font-semibold">
                    {opportunity.estimatedValue}
                  </span>
                </div>
                <h3 className="text-lg font-semibold text-primary-800 mb-2">
                  {opportunity.title}
                </h3>
                <p className="text-secondary-600 text-sm mb-2">
                  {opportunity.agency}
                </p>
                <p className="text-primary-700 text-sm font-medium">
                  Expected Release: {opportunity.expectedRelease}
                </p>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* CTA Section */}
      <section className="py-20 bg-primary-600 text-white">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <h2 className="text-3xl md:text-4xl font-bold mb-6">
            Ready to Bid on Government Contracts?
          </h2>
          <p className="text-xl mb-8 max-w-2xl mx-auto opacity-90">
            Partner with PUBUILD TECHNOLOGIES INC. for your government server solution needs. 
            Our proven track record speaks for itself.
          </p>
          <div className="flex flex-col sm:flex-row gap-4 justify-center">
            <Link
              href="/contact"
              className="inline-flex items-center px-8 py-3 bg-white text-primary-600 font-semibold rounded-lg hover:bg-secondary-100 transition-colors"
            >
              Contact Our Bidding Team
              <ArrowRight className="ml-2 h-5 w-5" />
            </Link>
            <Link
              href="/government"
              className="inline-flex items-center px-8 py-3 border-2 border-white text-white font-semibold rounded-lg hover:bg-white hover:text-primary-600 transition-colors"
            >
              Government Solutions
            </Link>
          </div>
        </div>
      </section>
    </div>
  )
}