// Type definitions for the website

export interface Product {
  id: string;
  name: string;
  category: string;
  description: string;
  specs: string[];
  compatibility?: string[];
  features: string[];
  applications?: string[];
  image?: string;
  datasheet?: string;
}

export interface ProductCategory {
  id: string;
  name: string;
  slug: string;
  description: string;
  icon?: string;
  products?: Product[];
}

export interface Solution {
  id: string;
  title: string;
  slug: string;
  description: string;
  features: string[];
  benefits: string[];
  useCases: string[];
  relatedProducts?: string[];
}

export interface ContactFormData {
  name: string;
  email: string;
  phone?: string;
  company?: string;
  message: string;
  consentPrivacy: boolean;
  newsletter?: boolean;
  honeypot?: string;
}

export interface RFPFormData {
  name: string;
  email: string;
  phone: string;
  agency: string;
  deadline: string;
  projectDescription: string;
  budgetRange?: string;
  specifications: string;
  attachments?: File[];
  consentPrivacy: boolean;
  honeypot?: string;
}

export interface BlogPost {
  id: string;
  title: string;
  slug: string;
  excerpt: string;
  content: string;
  date: string;
  author?: string;
  category?: string;
  tags?: string[];
}

export interface Certification {
  name: string;
  id: string;
  status: 'Active' | 'Pending' | 'Application Submitted';
  issuedDate?: string;
  expiryDate?: string;
}

export interface Location {
  name: string;
  address: string;
  city: string;
  state: string;
  zip: string;
  country: string;
  phone?: string;
  email?: string;
}

export interface FAQItem {
  question: string;
  answer: string;
  category?: string;
}

export interface Metadata {
  title: string;
  description: string;
  keywords?: string[];
  image?: string;
  url?: string;
}
