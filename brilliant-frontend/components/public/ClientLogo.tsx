import { useLocale } from "next-intl";
import type { Client } from "@/types";
import { initials } from "@/lib/utils";
import { localized } from "@/lib/localize";
import { SiteImage } from "@/components/ui/SiteImage";

export function ClientLogo({ client }: { client: Client }) {
  const locale = useLocale();
  return (
    <div className="flex h-24 min-w-[180px] items-center justify-center border border-line bg-white px-6">
      {client.logoPath ? (
        <SiteImage
          src={client.logoPath}
          alt={localized(locale, client.name, client.nameAr)}
          className="max-h-14 w-auto object-contain grayscale transition-all hover:grayscale-0"
        />
      ) : (
        <span className="font-headline text-lg font-bold uppercase text-neutral/40">
          {initials(localized(locale, client.name, client.nameAr))}
        </span>
      )}
    </div>
  );
}
