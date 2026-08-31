import { cn } from "@/lib/utils";

interface PageHeaderProps {
  title: string;
  subtitle?: string;
  eyebrow?: string;
  dark?: boolean;
  backgroundImage?: string;
}

export function PageHeader({ title, subtitle, eyebrow, dark = true, backgroundImage }: PageHeaderProps) {
  return (
    <section
      className={cn(
        "relative overflow-hidden pb-16 pt-24 md:pb-20 md:pt-28",
        dark ? "bg-neutral text-white" : "border-b border-line bg-white text-neutral",
      )}
    >
      {backgroundImage && (
        <>
          <div
            className="absolute inset-0 bg-cover bg-center"
            style={{ backgroundImage: `url('${backgroundImage}')` }}
          />
          <div className="absolute inset-0 bg-neutral/80" />
        </>
      )}
      <div className="container-brilliant relative z-10">
        {eyebrow && (
          <span className="font-headline text-xs font-semibold uppercase tracking-widest text-tertiary">
            {eyebrow}
          </span>
        )}
        <h1 className="mt-3 font-headline text-4xl font-bold uppercase leading-tight md:text-5xl">
          {title}
        </h1>
        <span className="mt-4 block h-[3px] w-16 bg-tertiary" aria-hidden="true" />
        {subtitle && (
          <p className={cn("mt-5 max-w-2xl leading-relaxed", dark ? "text-white/75" : "text-neutral/70")}>
            {subtitle}
          </p>
        )}
      </div>
    </section>
  );
}
