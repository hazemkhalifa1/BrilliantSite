import type { Config } from "tailwindcss";

const config: Config = {
  content: [
    "./pages/**/*.{js,ts,jsx,tsx,mdx}",
    "./components/**/*.{js,ts,jsx,tsx,mdx}",
    "./app/**/*.{js,ts,jsx,tsx,mdx}",
  ],
  theme: {
    extend: {
      colors: {
        primary: "#000000",
        secondary: "#0059BB",
        tertiary: "#BA1A1A",
        neutral: "#111C2D",
        "neutral-light": "#F5F5F5",
        line: "#E0E0E0",
        white: "#FFFFFF",
        surface: "#F9F9FF",
        "surface-container": "#E7EEFF",
        "on-surface": "#111C2D",
        "on-surface-variant": "#44474D",
        "secondary-container": "#0070EA",
        "on-secondary-container": "#FEFCFF",
        background: "#FFFFFF",
      },
      fontFamily: {
        headline: ["var(--font-headline)", "ui-sans-serif", "system-ui", "sans-serif"],
        body: ["var(--font-body)", "ui-sans-serif", "system-ui", "sans-serif"],
      },
      borderRadius: {
        DEFAULT: "0px",
        sm: "2px",
        md: "2px",
        lg: "2px",
      },
      boxShadow: {
        card: "none",
      },
    },
  },
  plugins: [],
};
export default config;
