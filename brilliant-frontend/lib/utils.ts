import { clsx, type ClassValue } from "clsx";
import { twMerge } from "tailwind-merge";

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs));
}

const LOCAL_ASSET_PREFIXES = ["/clients/"];

export function getImageUrl(path?: string | null): string {
  if (!path) return "/placeholder.svg";
  if (path.startsWith("http://") || path.startsWith("https://")) return path;
  if (LOCAL_ASSET_PREFIXES.some((prefix) => path.startsWith(prefix))) return path;
  const base = (process.env.NEXT_PUBLIC_API_URL || "http://localhost:5222/api").replace(/\/api$/, "");
  return base + path;
}

export function formatDate(value?: string | null, locale: string = "en-GB"): string {
  if (!value) return "—";
  const date = new Date(value);
  if (Number.isNaN(date.getTime())) return value;
  return date.toLocaleDateString(locale, {
    day: "numeric",
    month: "short",
    year: "numeric",
  });
}

export function truncate(text: string, maxLength = 120): string {
  if (!text) return "";
  if (text.length <= maxLength) return text;
  return `${text.slice(0, maxLength).trimEnd()}…`;
}

export function initials(name?: string | null): string {
  if (!name) return "?";
  return name
    .split(" ")
    .filter(Boolean)
    .slice(0, 2)
    .map((part) => part[0]?.toUpperCase())
    .join("");
}
