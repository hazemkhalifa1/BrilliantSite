"use client";

import { useEffect, useMemo, useRef, useState } from "react";

type ParsedValue = { number: number; decimals: number; suffix: string };

function parseValue(raw: string): ParsedValue | null {
  const trimmed = raw.trim();
  const match = trimmed.match(/^([+-]?\d+(?:\.\d+)?)(.*)$/);
  if (!match) return null;
  const decimals = match[1].includes(".") ? match[1].split(".")[1].length : 0;
  return { number: Number(match[1]), decimals, suffix: match[2] };
}

export function CountUp({
  value,
  className,
  duration = 1800,
}: {
  value: string;
  className?: string;
  duration?: number;
}) {
  const ref = useRef<HTMLSpanElement>(null);
  const parsed = useMemo(() => parseValue(value), [value]);
  const [display, setDisplay] = useState(0);

  useEffect(() => {
    const element = ref.current;
    if (!parsed || !element) {
      setDisplay(Number(parsed?.number ?? 0));
      return;
    }

    const reduceMotion =
      typeof window.matchMedia === "function" &&
      window.matchMedia("(prefers-reduced-motion: reduce)").matches;

    if (reduceMotion || typeof IntersectionObserver === "undefined") {
      setDisplay(parsed.number);
      return;
    }

    let frame = 0;
    const observer = new IntersectionObserver(
      (entries) => {
        const entry = entries[0];
        if (!entry.isIntersecting) return;
        observer.disconnect();

        const start = performance.now();
        const tick = (now: number) => {
          const progress = Math.min((now - start) / duration, 1);
          const eased = 1 - Math.pow(1 - progress, 3);
          setDisplay(parsed.number * eased);
          if (progress < 1) frame = requestAnimationFrame(tick);
        };
        frame = requestAnimationFrame(tick);
      },
      { threshold: 0.4 },
    );

    observer.observe(element);
    return () => {
      observer.disconnect();
      if (frame) cancelAnimationFrame(frame);
    };
  }, [parsed, duration]);

  if (!parsed) {
    return <span className={className}>{value}</span>;
  }

  return (
    <span className={className} ref={ref} dir="ltr">
      {display.toFixed(parsed.decimals)}
      {parsed.suffix}
    </span>
  );
}