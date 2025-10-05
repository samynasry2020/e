import type { Config } from "tailwindcss";

const config: Config = {
  content: [
    "./src/pages/**/*.{js,ts,jsx,tsx,mdx}",
    "./src/components/**/*.{js,ts,jsx,tsx,mdx}",
    "./src/app/**/*.{js,ts,jsx,tsx,mdx}",
  ],
  theme: {
    extend: {
      colors: {
        background: "var(--background)",
        foreground: "var(--foreground)",
        primary: {
          DEFAULT: "#1e40af",
          foreground: "#ffffff",
        },
        secondary: {
          DEFAULT: "#3b82f6",
          foreground: "#ffffff",
        },
        accent: {
          DEFAULT: "#f1f5f9",
          foreground: "#0f172a",
        },
        muted: {
          DEFAULT: "#f8fafc",
          foreground: "#64748b",
        },
        border: "#e2e8f0",
        input: "#e2e8f0",
        ring: "#1e40af",
      },
      fontFamily: {
        sans: ["Inter", "system-ui", "sans-serif"],
      },
    },
  },
  plugins: [],
};
export default config;