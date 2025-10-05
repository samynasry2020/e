export interface ProductCategory {
  id: string;
  name: string;
  description: string;
  slug: string;
  icon: string;
  features: string[];
  specifications?: Record<string, string>;
}

export interface Product {
  id: string;
  name: string;
  description: string;
  category: string;
  slug: string;
  image?: string;
  specifications: Record<string, string>;
  features: string[];
  compatibility?: string[];
}

export interface Solution {
  id: string;
  name: string;
  description: string;
  slug: string;
  features: string[];
  benefits: string[];
  caseStudies?: Array<{
    title: string;
    description: string;
    results: string[];
  }>;
}

export interface GovernmentInfo {
  naicsCodes: Array<{
    code: string;
    description: string;
    certification?: string;
  }>;
  certifications: Array<{
    name: string;
    status: "active" | "pending" | "expired";
    certificationId?: string;
    expiryDate?: string;
  }>;
  pscCodes?: string[];
  capabilityStatement: {
    title: string;
    content: string;
    differentiators: string[];
  };
}

export interface ContactForm {
  name: string;
  email: string;
  phone?: string;
  company?: string;
  message: string;
  consent: boolean;
}

export interface RFPForm {
  agency: string;
  deadline: string;
  projectTitle: string;
  description: string;
  budget?: string;
  requirements: string;
  attachments?: File[];
  contactName: string;
  contactEmail: string;
  contactPhone?: string;
  consent: boolean;
}