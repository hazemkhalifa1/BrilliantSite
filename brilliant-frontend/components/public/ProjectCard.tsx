import { useLocale } from "next-intl";
import { Link } from "@/src/i18n/navigation";
import { Calendar, User } from "lucide-react";
import type { Project } from "@/types";
import { truncate } from "@/lib/utils";
import { localized } from "@/lib/localize";
import { Badge } from "@/components/ui/Badge";
import { SiteImage } from "@/components/ui/SiteImage";

export function ProjectCard({ project }: { project: Project }) {
  const locale = useLocale();
  return (
    <Link
      href={`/projects/${project.id}`}
      className="card-brilliant group block h-full overflow-hidden transition-all duration-300 hover:-translate-y-1 hover:border-tertiary hover:shadow-[6px_6px_0px_0px_#0059bb]"
    >
      <div className="relative aspect-[4/3] overflow-hidden bg-neutral-light">
        <SiteImage
          src={project.imagePath}
          alt={localized(locale, project.title, project.titleAr)}
          className="h-full w-full transition-transform duration-300 group-hover:scale-105"
        />
      </div>
      <div className="p-5">
        <div className="flex items-center gap-2">
          <Badge variant="tertiary">{project.typeName}</Badge>
          <span className="font-headline text-xs uppercase tracking-widest text-neutral/50">
            {project.year}
          </span>
        </div>
        <h3 className="mt-3 font-headline text-lg font-bold uppercase transition-colors group-hover:text-secondary">
          {localized(locale, project.title, project.titleAr)}
        </h3>
        <p className="mt-2 text-sm leading-relaxed text-neutral/70">
          {truncate(localized(locale, project.description, project.descriptionAr), 110)}
        </p>
        <div className="mt-4 flex items-center gap-4 text-xs text-neutral/60">
          {project.clientName && (
            <span className="inline-flex items-center gap-1.5">
              <User className="h-3.5 w-3.5" />
              {localized(locale, project.clientName, project.clientNameAr)}
            </span>
          )}
          <span className="inline-flex items-center gap-1.5">
            <Calendar className="h-3.5 w-3.5" />
            {project.year}
          </span>
        </div>
      </div>
    </Link>
  );
}
