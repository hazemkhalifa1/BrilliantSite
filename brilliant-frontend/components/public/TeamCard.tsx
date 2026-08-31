import { useLocale } from "next-intl";
import type { TeamMember } from "@/types";
import { initials } from "@/lib/utils";
import { localized } from "@/lib/localize";
import { SiteImage } from "@/components/ui/SiteImage";

export function TeamCard({ member }: { member: TeamMember }) {
  const locale = useLocale();
  return (
    <div className="card-brilliant group overflow-hidden transition-colors hover:border-tertiary">
      <div className="relative aspect-[4/5] overflow-hidden bg-neutral-light">
        {member.imagePath ? (
          <SiteImage
            src={member.imagePath}
            alt={localized(locale, member.name, member.nameAr)}
            className="h-full w-full transition-transform duration-300 group-hover:scale-105"
          />
        ) : (
          <div className="flex h-full w-full items-center justify-center bg-secondary/10 font-headline text-5xl font-bold text-secondary">
            {initials(localized(locale, member.name, member.nameAr))}
          </div>
        )}
      </div>
      <div className="p-5">
        <h3 className="font-headline text-lg font-bold uppercase">{localized(locale, member.name, member.nameAr)}</h3>
        <span className="mt-1 block h-[3px] w-8 bg-tertiary" aria-hidden="true" />
        <p className="mt-2 text-sm text-neutral/70">{localized(locale, member.jobTitle, member.jobTitleAr)}</p>
      </div>
    </div>
  );
}
