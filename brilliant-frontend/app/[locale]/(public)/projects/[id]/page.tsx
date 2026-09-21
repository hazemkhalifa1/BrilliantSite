import type { Metadata } from "next";
import { Link } from "@/src/i18n/navigation";
import { getLocale, getTranslations } from "next-intl/server";
import { notFound } from "next/navigation";
import { Calendar, User, ArrowLeft } from "lucide-react";
import { localeAlternates } from "@/lib/seo";
import { apiFetch } from "@/lib/api";
import { getImageUrl } from "@/lib/utils";
import { localized } from "@/lib/localize";
import type { Project } from "@/types";
import { Badge } from "@/components/ui/Badge";
import { SiteImage } from "@/components/ui/SiteImage";
import { Reveal } from "@/components/public/Reveal";
import { Parallax } from "@/components/public/Parallax";

export const dynamic = "force-dynamic";

interface ProjectPageProps {
  params: { id: string; locale: string };
}

async function getProject(id: string): Promise<Project | null> {
  return apiFetch<Project>(`/projects/${id}`).catch(() => null);
}

export async function generateMetadata({ params }: ProjectPageProps): Promise<Metadata> {
  const t = await getTranslations({ locale: params.locale, namespace: "projectDetail" });
  const project = await getProject(params.id);
  if (!project) return { title: t("notFound") };
  const locale = await getLocale();
  return {
    title: localized(locale, project.title, project.titleAr),
    description: localized(locale, project.description, project.descriptionAr),
    alternates: localeAlternates(locale, `/projects/${params.id}`),
    openGraph: {
      title: localized(locale, project.title, project.titleAr),
      description: localized(locale, project.description, project.descriptionAr),
      images: [getImageUrl(project.imagePath)],
    },
  };
}

export default async function ProjectPage({ params }: ProjectPageProps) {
  const locale = await getLocale();
  const t = await getTranslations("projectDetail");
  const project = await getProject(params.id);
  if (!project) notFound();

  return (
    <article>
      <div className="relative aspect-[21/9] overflow-hidden bg-neutral-light">
        <Parallax speed={0.06} className="absolute inset-0">
          <SiteImage src={project.imagePath} alt={localized(locale, project.title, project.titleAr)} className="h-full w-full" eager />
        </Parallax>
      </div>

      <div className="container-brilliant py-12 md:py-16">
        <Reveal>
          <Link
            href="/projects"
            className="inline-flex items-center gap-1.5 font-headline text-xs font-semibold uppercase tracking-wide text-secondary transition-colors hover:text-tertiary"
          >
            <ArrowLeft className="h-4 w-4" />
            {t("allProjects")}
          </Link>
        </Reveal>

        <Reveal delay={80}>
          <div className="mt-6 flex flex-wrap items-center gap-3">
          <Badge variant="tertiary">{project.typeName}</Badge>
          <span className="inline-flex items-center gap-1.5 text-sm text-neutral/60">
            <Calendar className="h-4 w-4" />
            {project.year}
          </span>
          {project.clientName && (
            <span className="inline-flex items-center gap-1.5 text-sm text-neutral/60">
              <User className="h-4 w-4" />
              {localized(locale, project.clientName, project.clientNameAr)}
            </span>
          )}
        </div>
        </Reveal>

        <Reveal from="up">
          <h1 className="mt-4 font-headline text-3xl font-bold uppercase leading-tight md:text-5xl">
            {localized(locale, project.title, project.titleAr)}
          </h1>
          <span className="mt-4 block h-[3px] w-16 bg-tertiary" aria-hidden="true" />
        </Reveal>

        <Reveal from="left" delay={140}>
          <div className="mt-8 max-w-3xl space-y-4 leading-relaxed text-neutral/80">
            {localized(locale, project.description, project.descriptionAr).split(/\n+/).map((paragraph, index) => (
              <p key={index}>{paragraph}</p>
            ))}
          </div>
        </Reveal>

        <div className="mt-12 grid gap-4 sm:grid-cols-3">
          {[
            { label: t("type"), value: project.typeName },
            { label: t("client"), value: localized(locale, project.clientName, project.clientNameAr) || "—" },
            { label: t("year"), value: project.year },
          ].map((item, index) => (
            <Reveal key={item.label} delay={index * 90}>
              <div
                className="card-brilliant pop-card h-full bg-neutral-light p-6"
                style={{ transitionDelay: `${index * 90 + 100}ms` }}
              >
                <p className="font-headline text-xs font-semibold uppercase tracking-widest text-neutral/50">
                  {item.label}
                </p>
                <p className="mt-2 font-headline text-lg font-bold uppercase">{item.value}</p>
              </div>
            </Reveal>
          ))}
        </div>
      </div>
    </article>
  );
}
