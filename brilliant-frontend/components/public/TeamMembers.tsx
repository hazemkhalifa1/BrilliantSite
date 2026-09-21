"use client";

import type { TeamMember } from "@/types";
import { TeamCard } from "./TeamCard";
import { Reveal } from "./Reveal";

export function TeamMembers({
  featured,
  supporting,
  tagline,
}: {
  featured: TeamMember | null;
  supporting: TeamMember[];
  tagline?: string;
}) {
  return (
    <div className="space-y-12 md:space-y-16">
      {featured && (
        <Reveal from="left">
          <TeamCard member={featured} variant="featured" tagline={tagline} />
        </Reveal>
      )}

      {supporting.length > 0 && (
        <div className="grid gap-5 sm:grid-cols-2 lg:grid-cols-3">
          {supporting.map((member, index) => (
            <Reveal key={member.id} delay={index * 80} from={index % 2 === 0 ? "left" : "right"}>
              <div
                className="pop-card h-full"
                style={{ transitionDelay: `${index * 80 + 120}ms` }}
              >
                <TeamCard member={member} variant="row" />
              </div>
            </Reveal>
          ))}
        </div>
      )}
    </div>
  );
}