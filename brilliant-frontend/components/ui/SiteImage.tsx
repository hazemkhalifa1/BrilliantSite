import { getImageUrl } from "@/lib/utils";
import { cn } from "@/lib/utils";

interface SiteImageProps {
  src?: string | null;
  alt: string;
  className?: string;
  eager?: boolean;
}

export function SiteImage({ src, alt, className, eager = false }: SiteImageProps) {
  return (
    // eslint-disable-next-line @next/next/no-img-element
    <img
      src={getImageUrl(src)}
      alt={alt}
      loading={eager ? "eager" : "lazy"}
      className={cn("object-cover", className)}
    />
  );
}
