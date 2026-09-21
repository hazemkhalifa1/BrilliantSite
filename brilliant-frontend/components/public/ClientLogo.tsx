"use client";

import { useEffect, useRef } from "react";
import { useLocale } from "next-intl";
import type { Client } from "@/types";
import { initials } from "@/lib/utils";
import { localized } from "@/lib/localize";
import { SiteImage } from "@/components/ui/SiteImage";

export function ClientLogo({ client, index = 0, delay = 0 }: { client: Client; index?: number; delay?: number }) {
  const locale = useLocale();
  const ref = useRef<HTMLDivElement>(null);
  const name = localized(locale, client.name, client.nameAr);

  useEffect(() => {
    const element = ref.current;
    if (!element) return;

    if (typeof IntersectionObserver === "undefined") {
      element.classList.add("visible");
      return;
    }

    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          if (entry.isIntersecting) {
            element.classList.add("visible");
            observer.unobserve(element);
          }
        });
      },
      { threshold: 0.1, rootMargin: "0px 0px -50px 0px" },
    );

    observer.observe(element);
    return () => observer.disconnect();
  }, []);

  return (
    <div
      ref={ref}
      className="neo-client reveal-on-scroll"
      style={delay ? { transitionDelay: `${delay}ms` } : undefined}
    >
      <span className="neo-client__index" aria-hidden="true">
        {String(index + 1).padStart(2, "0")}
      </span>
      {client.logoPath ? (
        <SiteImage src={client.logoPath} alt={name} className="neo-client__logo" />
      ) : (
        <span className="neo-client__initials">{initials(name)}</span>
      )}
      <span className="neo-client__name">{name}</span>
    </div>
  );
}