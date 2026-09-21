import createNextIntlPlugin from "next-intl/plugin";

/** @type {import('next').NextConfig} */
const nextConfig = {
  images: {
    formats: ["image/avif", "image/webp"],
    remotePatterns: [
      { protocol: "http", hostname: "**" },
      { protocol: "https", hostname: "**" },
    ],
  },
  async redirects() {
    return [
      {
        source: "/en/blog/what-is-an-hvac-system-and-how-does-it-work-2",
        destination: "/en/blog/what-is-an-hvac-system-and-how-does-it-work",
        permanent: true,
      },
      {
        source: "/ar/blog/what-is-an-hvac-system-and-how-does-it-work-2",
        destination: "/ar/blog/what-is-an-hvac-system-and-how-does-it-work",
        permanent: true,
      },
    ];
  },
};

const withNextIntl = createNextIntlPlugin("./src/i18n/request.ts");

export default withNextIntl(nextConfig);
