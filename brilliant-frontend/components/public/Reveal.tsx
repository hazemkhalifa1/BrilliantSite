"use client";

import * as React from "react";
import { cn } from "@/lib/utils";

export interface RevealProps {
  children: React.ReactNode;
  className?: string;
  delay?: number;
  from?: "up" | "left" | "right";
}

export function Reveal({ children, className, delay = 0, from = "up" }: RevealProps) {
  const ref = React.useRef<HTMLDivElement>(null);

  React.useEffect(() => {
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
      className={cn(
        "reveal-on-scroll",
        from === "left" && "reveal-from-left",
        from === "right" && "reveal-from-right",
        className,
      )}
      style={delay ? { transitionDelay: `${delay}ms` } : undefined}
    >
      {children}
    </div>
  );
}
