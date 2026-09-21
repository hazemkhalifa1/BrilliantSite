"use client";

import { useState } from "react";
import { useLocale, useTranslations } from "next-intl";
import type { TeamMember } from "@/types";
import { localized } from "@/lib/localize";
import { SiteImage } from "@/components/ui/SiteImage";

type TeamCardVariant = "featured" | "row";

function memberInitials(name: string): string {
  const parts = name.split(/\s+/).filter(Boolean);
  const meaningful = parts.filter((part) => !/\.$/.test(part));
  const list = meaningful.length > 0 ? meaningful : parts;
  const first = list[0]?.[0] ?? "";
  const last = list.length > 1 ? list[list.length - 1][0] : "";
  return (first + last).toUpperCase();
}

function MemberMedia({ member, name }: { member: TeamMember; name: string }) {
  const [failed, setFailed] = useState(false);
  const showFallback = !member.imagePath || failed;

  if (showFallback) {
    return (
      <div className="neo-fallback">
        <span>{memberInitials(name)}</span>
      </div>
    );
  }

  return (
    <SiteImage src={member.imagePath} alt={name} onError={() => setFailed(true)} />
  );
}

function FeaturedCard({
  member,
  name,
  role,
  description,
  tagline,
}: {
  member: TeamMember;
  name: string;
  role: string;
  description: string;
  tagline?: string;
}) {
  const t = useTranslations("team");
  const monogram = memberInitials(name);

  return (
    <article className="neo-featured">
      <div className="neo-featured__media">
        <MemberMedia member={member} name={name} />
      </div>

      <div className="neo-featured__info">
        <span className="neo-featured__monogram" aria-hidden="true">
          {monogram}
        </span>
        <span className="neo-featured__index">{t("eyebrow")}</span>
        <span className="neo-featured__role">{role}</span>
        <h2 className="neo-featured__name">{name}</h2>
        <span className="neo-featured__accent" aria-hidden="true" />
        {description ? (
          <p className="neo-featured__desc">
            <span className="mt-5 block">{description}</span>
          </p>
        ) : null}
        {tagline ? <p className="neo-featured__tagline">{tagline}</p> : null}
      </div>
    </article>
  );
}

function RowCard({
  member,
  name,
  role,
  description,
}: {
  member: TeamMember;
  name: string;
  role: string;
  description: string;
}) {
  return (
    <article className="neo-team-card group h-full">
      <div className="neo-team-card__media">
        <MemberMedia member={member} name={name} />
      </div>
      <div className="neo-team-card__body">
        <div className="neo-team-card__role">{role}</div>
        <h3 className="neo-team-card__name">{name}</h3>
        <span className="neo-team-card__accent" aria-hidden="true" />
        {description ? (
          <p className="neo-team-card__desc">
            <span className="mt-3 block">{description}</span>
          </p>
        ) : null}
      </div>
    </article>
  );
}

export function TeamCard({
  member,
  variant = "row",
  tagline,
}: {
  member: TeamMember;
  variant?: TeamCardVariant;
  tagline?: string;
}) {
  const locale = useLocale();
  const name = localized(locale, member.name, member.nameAr);
  const role = localized(locale, member.jobTitle, member.jobTitleAr);
  const description = localized(locale, member.description, member.descriptionAr);

  if (variant === "featured") {
    return (
      <FeaturedCard
        member={member}
        name={name}
        role={role}
        description={description}
        tagline={tagline}
      />
    );
  }
  return <RowCard member={member} name={name} role={role} description={description} />;
}