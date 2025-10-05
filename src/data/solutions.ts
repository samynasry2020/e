import { Solution } from "@/types";

export const solutions: Solution[] = [
  {
    id: "ai-hpc",
    name: "AI/HPC Infrastructure",
    description: "Complete infrastructure solutions for artificial intelligence and high-performance computing workloads",
    slug: "ai-hpc",
    features: [
      "GPU cluster design and deployment",
      "High-speed interconnects (InfiniBand, RoCE)",
      "Storage solutions for large datasets",
      "Cooling and power optimization",
      "Software stack integration",
      "Performance monitoring and optimization"
    ],
    benefits: [
      "Accelerated AI training and inference",
      "Reduced time-to-insight for research",
      "Scalable infrastructure that grows with your needs",
      "Optimized power and cooling efficiency",
      "24/7 monitoring and support"
    ],
    caseStudies: [
      {
        title: "Federal Research Laboratory AI Upgrade",
        description: "Deployed GPU cluster for machine learning research",
        results: [
          "50% faster model training times",
          "200% increase in research throughput",
          "Reduced energy consumption by 30%"
        ]
      }
    ]
  },
  {
    id: "data-center",
    name: "Data Center Builds",
    description: "End-to-end data center design, deployment, and management services",
    slug: "data-center",
    features: [
      "Site assessment and planning",
      "Power and cooling design",
      "Network architecture",
      "Server and storage deployment",
      "Monitoring and management systems",
      "Security implementation"
    ],
    benefits: [
      "Optimized for your specific workload requirements",
      "Scalable design for future growth",
      "Energy-efficient operations",
      "High availability and redundancy",
      "Comprehensive documentation and training"
    ],
    caseStudies: [
      {
        title: "Government Agency Data Center Modernization",
        description: "Complete data center refresh for federal agency",
        results: [
          "99.9% uptime achieved",
          "40% reduction in power consumption",
          "Improved security compliance"
        ]
      }
    ]
  },
  {
    id: "workstation-fleets",
    name: "Workstation Fleets",
    description: "Large-scale workstation deployment and management for enterprises and government agencies",
    slug: "workstation-fleets",
    features: [
      "Bulk procurement and configuration",
      "Custom imaging and deployment",
      "Asset tracking and management",
      "Remote support and maintenance",
      "Lifecycle management",
      "Security policy enforcement"
    ],
    benefits: [
      "Consistent configurations across all devices",
      "Reduced deployment time and costs",
      "Centralized management and monitoring",
      "Proactive maintenance and support",
      "Secure and compliant deployments"
    ],
    caseStudies: [
      {
        title: "Federal Agency Workstation Refresh",
        description: "Deployed 500+ workstations across multiple locations",
        results: [
          "Zero downtime during migration",
          "All devices configured identically",
          "Reduced support tickets by 60%"
        ]
      }
    ]
  }
];