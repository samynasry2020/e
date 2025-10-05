import { GovernmentInfo } from "@/types";

export const governmentInfo: GovernmentInfo = {
  naicsCodes: [
    {
      code: "334111",
      description: "Electronic Computer Manufacturing",
      certification: "Active"
    },
    {
      code: "423430",
      description: "Computer and Computer Peripheral Equipment and Software Merchant Wholesalers",
      certification: "Active"
    },
    {
      code: "541512",
      description: "Computer Systems Design Services",
      certification: "Active"
    },
    {
      code: "334112",
      description: "Computer Storage Device Manufacturing",
      certification: "Pending"
    },
    {
      code: "541519",
      description: "Other Computer Related Services",
      certification: "Active"
    }
  ],
  certifications: [
    {
      name: "Small Business Administration (SBA) - Small Business",
      status: "active",
      certificationId: "SB-2024-001",
      expiryDate: "2025-12-31"
    },
    {
      name: "California Department of General Services (DGS) - Small Business",
      status: "active",
      certificationId: "DGS-SB-2024-456",
      expiryDate: "2025-06-30"
    },
    {
      name: "SBA Woman-Owned Small Business (WOSB)",
      status: "pending",
      certificationId: "Pending Review"
    }
  ],
  pscCodes: [
    "7B20", "7B21", "7B22", "7C20", "7C21", "7D20", "7E20", "7F20",
    "7G20", "7G21", "7H20", "7J20", "7K20", "7L20", "7M20"
  ],
  capabilityStatement: {
    title: "Pubuild Capability Statement",
    content: `Pubuild is a leading provider of IT hardware solutions and government contracting services. We specialize in delivering high-performance computing infrastructure, enterprise servers, professional workstations, and comprehensive technology solutions to federal agencies and commercial clients.

Our expertise spans:

• Enterprise Server Solutions
• High-Performance Computing (HPC) Infrastructure
• AI and Machine Learning Platforms
• Data Center Design and Deployment
• Workstation Fleet Management
• Network Infrastructure
• Storage Solutions
• Professional Services

We maintain active certifications and compliance with federal acquisition requirements, ensuring our clients receive reliable, secure, and cost-effective technology solutions.`,
    differentiators: [
      "Deep experience with federal procurement processes",
      "Technical expertise in enterprise computing",
      "Strong vendor relationships for competitive pricing",
      "Comprehensive project management capabilities",
      "24/7 technical support and maintenance",
      "Security-cleared personnel available"
    ]
  }
};