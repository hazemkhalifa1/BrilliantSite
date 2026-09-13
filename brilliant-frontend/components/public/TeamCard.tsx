"use client";

import { useState } from "react";
import { useLocale } from "next-intl";
import type { TeamMember } from "@/types";
import { localized } from "@/lib/localize";
import { truncate } from "@/lib/utils";
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

function InitialsFallback({
  name,
  sizeClassName = "text-5xl",
}: {
  name: string;
  sizeClassName?: string;
}) {
  return (
    <div
      className={`absolute inset-0 flex items-center justify-center bg-secondary/10 font-headline font-bold text-secondary ${sizeClassName}`}
    >
      {memberInitials(name)}
    </div>
  );
}

// Registration crosshair — the drawing-sheet signature.
function CornerMark({ className }: { className?: string }) {
  return (
    <span
      aria-hidden="true"
      className={`pointer-events-none absolute select-none font-mono text-[11px] font-medium leading-none text-neutral/40 ${className ?? ""}`}
    >
      +
    </span>
  );
}

function MemberImage({
  member,
  name,
  className,
  sizeClassName,
  marks = true,
}: {
  member: TeamMember;
  name: string;
  className?: string;
  sizeClassName?: string;
  marks?: boolean;
}) {
  const [failed, setFailed] = useState(false);
  const showFallback = !member.imagePath || failed;

  return (
    <div className={`relative overflow-hidden bg-neutral-light blueprint-grid ${className ?? ""}`}>
      {!showFallback ? (
        <SiteImage
          src={member.imagePath}
          alt={name}
          className="team-photo absolute inset-0 h-full w-full object-contain"
          onError={() => setFailed(true)}
        />
      ) : (
        <InitialsFallback name={name} sizeClassName={sizeClassName} />
      )}

      {marks && (
        <>
          <CornerMark className="start-4 top-4" />
          <CornerMark className="bottom-4 end-4" />
        </>
      )}
    </div>
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
  const monogram = memberInitials(name);
  return (
    <article className="team-card card-brilliant grid overflow-hidden lg:grid-cols-12">
      <MemberImage
        member={member}
        name={name}
        className="aspect-[4/3] lg:col-span-7 lg:aspect-auto lg:min-h-[440px]"
        sizeClassName="text-6xl"
      />

      <div className="relative flex flex-col justify-between overflow-hidden bg-neutral p-8 text-white md:p-10 lg:col-span-5 lg:p-12">
        <span
          aria-hidden="true"
          className="team-monogram pointer-events-none absolute -bottom-8 -right-4 select-none font-headline text-[10rem] font-bold leading-none text-white/5 md:text-[12rem]"
        >
          {monogram}
        </span>

        <div className="relative">
          <span aria-hidden="true" className="block h-[3px] w-16 bg-tertiary" />
          <h2 className="mt-6 font-headline text-3xl font-bold uppercase leading-[1.05] tracking-tight md:text-4xl">
            {name}
          </h2>
          <p className="mt-4 font-headline text-sm font-semibold uppercase tracking-widest text-tertiary">
            {role}
          </p>
          {description ? (
            <p className="mt-6 max-w-md text-sm leading-relaxed text-white/70">{description}</p>
          ) : null}
        </div>

        {tagline && (
          <p className="relative mt-10 font-headline text-xs font-semibold uppercase tracking-widest text-white/50">
            {tagline}
          </p>
        )}
      </div>
    </article>
  );
}

// Supporting members — photo thumbnail + services-style content.
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
    <article className="team-card card-brilliant group grid h-full overflow-hidden transition-colors duration-500 hover:border-tertiary sm:grid-cols-[200px_1fr]">
      <MemberImage
        member={member}
        name={name}
        className="h-44 sm:h-auto"
        sizeClassName="text-3xl"
        marks={false}
      />

      <div className="flex flex-col justify-center p-6 md:p-8">
        <span className="font-headline text-xs font-semibold uppercase tracking-widest text-neutral/50">
          {role}
        </span>
        <h3 className="mt-3 font-headline text-lg font-bold uppercase leading-tight md:text-xl">
          {name}
        </h3>
        <span aria-hidden="true" className="team-accent mt-3 block h-[3px] w-8 bg-tertiary" />
        {description ? (
          <p className="mt-3 text-sm leading-relaxed text-neutral/70">{truncate(description, 180)}</p>
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
