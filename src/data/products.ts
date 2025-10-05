import { ProductCategory, Product } from "@/types";

export const productCategories: ProductCategory[] = [
  {
    id: "servers",
    name: "Servers",
    description: "High-performance servers for enterprise and data center applications",
    slug: "servers",
    icon: "Server",
    features: [
      "Rackmount and tower form factors",
      "Latest Intel and AMD processors",
      "Enterprise-grade reliability",
      "Scalable storage options",
      "Remote management capabilities"
    ]
  },
  {
    id: "workstations",
    name: "Workstations",
    description: "Professional workstations for demanding computational tasks",
    slug: "workstations",
    icon: "Cpu",
    features: [
      "Multi-core processor support",
      "Professional graphics cards",
      "ECC memory configurations",
      "High-speed storage options",
      "Certified for professional applications"
    ]
  },
  {
    id: "gpus",
    name: "GPUs",
    description: "Graphics processing units for AI, HPC, and visualization",
    slug: "gpus",
    icon: "Cpu",
    features: [
      "NVIDIA and AMD options",
      "CUDA and OpenCL support",
      "High memory bandwidth",
      "Multi-GPU configurations",
      "AI and machine learning optimized"
    ]
  },
  {
    id: "storage",
    name: "Storage",
    description: "Enterprise storage solutions for data management and backup",
    slug: "storage",
    icon: "HardDrive",
    features: [
      "SSD and HDD options",
      "RAID configurations",
      "NAS and SAN solutions",
      "Cloud integration",
      "Data protection features"
    ]
  },
  {
    id: "networking",
    name: "Networking",
    description: "Network infrastructure equipment and connectivity solutions",
    slug: "networking",
    icon: "Network",
    features: [
      "Switches and routers",
      "Wireless solutions",
      "Security appliances",
      "Load balancers",
      "Network monitoring tools"
    ]
  },
  {
    id: "monitors",
    name: "Monitors",
    description: "Professional displays for various applications and environments",
    slug: "monitors",
    icon: "Monitor",
    features: [
      "High resolution displays",
      "Color accuracy",
      "Multiple connectivity options",
      "Ergonomic designs",
      "Professional calibration"
    ]
  }
];

export const featuredProducts: Product[] = [
  {
    id: "server-1",
    name: "Enterprise Rack Server X1",
    description: "High-performance 2U rackmount server with dual processor support",
    category: "servers",
    slug: "enterprise-rack-server-x1",
    specifications: {
      "Form Factor": "2U Rackmount",
      "Processors": "Dual Intel Xeon Scalable",
      "Memory": "Up to 2TB DDR4 ECC",
      "Storage": "Up to 16 x 2.5\" drives",
      "Network": "Dual 10GbE + management port",
      "Power Supply": "Redundant 800W PSU"
    },
    features: [
      "Hot-swappable drives",
      "Redundant power supplies",
      "Remote management (IPMI)",
      "RAID controller support",
      "Tool-less maintenance"
    ],
    compatibility: [
      "VMware ESXi",
      "Microsoft Windows Server",
      "Red Hat Enterprise Linux",
      "Ubuntu Server LTS"
    ]
  },
  {
    id: "workstation-1",
    name: "Professional Workstation Pro",
    description: "High-end workstation for CAD, 3D modeling, and content creation",
    category: "workstations",
    slug: "professional-workstation-pro",
    specifications: {
      "Processor": "AMD Threadripper PRO",
      "Memory": "Up to 2TB DDR4 ECC",
      "Graphics": "NVIDIA RTX A6000",
      "Storage": "2TB NVMe SSD + 4TB HDD",
      "OS": "Windows 11 Pro / Linux"
    },
    features: [
      "ECC memory support",
      "Professional GPU options",
      "Multiple PCIe slots",
      "Thunderbolt connectivity",
      "Quiet operation"
    ],
    compatibility: [
      "AutoCAD",
      "SolidWorks",
      "Adobe Creative Suite",
      "Blender",
      "MATLAB"
    ]
  }
];