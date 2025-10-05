// Brand and Company Constants
export const COMPANY = {
  name: 'PU Build',
  legalName: 'PU Build, Inc.',
  phone: '(800) 474-1388',
  phoneRaw: '8004741388',
  email: 'sales@pubuild.com',
  supportEmail: 'support@pubuild.com',
  domain: 'pubuild.com',
  url: 'https://pubuild.com',
  
  // Placeholders for government contracting (to be filled by client)
  uei: '[UEI-PLACEHOLDER]',
  cage: '[CAGE-PLACEHOLDER]',
  duns: '[DUNS-PLACEHOLDER]',
  
  // NAICS Codes
  naicsCodes: [
    { code: '334111', description: 'Electronic Computer Manufacturing' },
    { code: '423430', description: 'Computer and Computer Peripheral Equipment and Software Merchant Wholesalers' },
    { code: '541512', description: 'Computer Systems Design Services' },
    { code: '541519', description: 'Other Computer Related Services' },
    { code: '811212', description: 'Computer and Office Machine Repair and Maintenance' },
  ],
  
  // PSC Codes (Product Service Codes) - Federal procurement
  pscCodes: ['7B', '7C', '7D', '7E', '7F', '7G'],
  
  // Business certifications (update as obtained)
  certifications: [] as Array<{
    name: string;
    id: string;
    status: 'Active' | 'Pending' | 'Application Submitted';
    issuedDate?: string;
    expiryDate?: string;
  }>,
  // Example: { name: 'California DGS Small Business', id: 'CERT-ID-HERE', status: 'Active' },
  // { name: 'WOSB (Women-Owned Small Business)', id: 'Pending', status: 'Application Submitted' },
  
  // Office locations
  locations: [
    {
      name: 'Corporate Headquarters',
      address: '[Physical Address Line 1]',
      city: '[City]',
      state: '[State]',
      zip: '[ZIP]',
      country: 'United States',
    },
  ],
  
  // Business hours
  hours: {
    weekdays: 'Monday - Friday: 8:00 AM - 6:00 PM PT',
    weekend: 'Saturday - Sunday: Closed',
    support: '24/7 Emergency Support Available',
  },
  
  // SLA
  responseSLA: '24 hours for general inquiries, 4 hours for RFP/bid requests',
};

// Color Palette
export const COLORS = {
  primary: '#0066CC',
  primaryDark: '#0047AB',
  secondary: '#00A3E0',
  accent: '#FF6B35',
  success: '#28A745',
  warning: '#FFC107',
  error: '#DC3545',
};

// SEO Keywords
export const SEO_KEYWORDS = [
  'government IT bids',
  'servers for agencies',
  'rackmount servers',
  'AI infrastructure',
  'HPC infrastructure',
  'data center solutions',
  'enterprise workstations',
  'GPU servers',
  'storage solutions',
  'networking equipment',
  'NAICS 334111',
  'NAICS 541512',
  'IT hardware supplier',
  'government contracting IT',
];

// Social Media (to be configured)
export const SOCIAL_MEDIA = {
  linkedin: '',
  twitter: '',
  facebook: '',
};

// Legal Disclaimer
export const DISCLAIMERS = {
  government: 'We are an independent supplier. No government endorsement or affiliation is implied. We are not an official government contractor without proper documentation.',
  export: 'Products may be subject to U.S. export controls. Purchaser is responsible for compliance with all applicable export regulations.',
  pricing: 'All pricing is subject to change. Contact us for current quotes and availability.',
  warranty: 'Product warranties are provided by manufacturers. PU Build acts as a reseller and facilitates warranty service.',
};
