import { Link } from "@/src/i18n/navigation";
import { getTranslations } from "next-intl/server";
import {
  ArrowRight,
  Building2,
  FolderKanban,
  Handshake,
  MessageSquareQuote,
  Newspaper,
  Package,
  Settings,
  Users,
} from "lucide-react";
import { adminFetch, adminFetchList } from "@/lib/adminApi";
import { AdminPageHeader } from "@/components/admin/AdminPageHeader";
import type { ContactInfo } from "@/types";

export const dynamic = "force-dynamic";

interface AdminDashboardPageProps {
  searchParams: { page?: string };
}

export default async function AdminDashboardPage({ searchParams }: AdminDashboardPageProps) {
  const t = await getTranslations("admin.dashboard");
  const pageIndex =
    searchParams.page && !Number.isNaN(Number(searchParams.page)) ? Number(searchParams.page) : 1;

  const SECTION_DEFS = [
    { label: t("labels.services"), href: "/admin/services", endpoint: "/services?pageSize=1", icon: Building2 },
    { label: t("labels.projects"), href: "/admin/projects", endpoint: "/projects?pageSize=1", icon: FolderKanban },
    { label: t("labels.products"), href: "/admin/products", endpoint: "/products?pageSize=1", icon: Package },
    { label: t("labels.blog"), href: "/admin/blog", endpoint: "/blog?pageSize=1", icon: Newspaper },
    { label: t("labels.team"), href: "/admin/team", endpoint: "/team?pageSize=1", icon: Users },
    { label: t("labels.clients"), href: "/admin/clients", endpoint: "/clients?pageSize=1", icon: Handshake },
    {
      label: t("labels.testimonials"),
      href: "/admin/testimonials",
      endpoint: "/testimonials?pageSize=1",
      icon: MessageSquareQuote,
    },
  ];

  const stats = await Promise.all(
    SECTION_DEFS.map(async (section) => {
      const data = await adminFetchList(section.endpoint).catch(() => ({ items: [], totalCount: 0 }));
      return { ...section, totalCount: data.totalCount };
    }),
  );

  const contact = await adminFetch<ContactInfo>("/contact").catch(() => null);

  const messageCount = 0;

  return (
    <div className="space-y-8">
      <AdminPageHeader
        title={t("title")}
        description={t("description")}
        action={
          <Link href="/admin/settings" className="btn-secondary !px-4 !py-2">
            {t("siteSettings")}
          </Link>
        }
      />

      <section className="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
        {stats.map((stat) => {
          const Icon = stat.icon;
          return (
            <Link
              key={stat.href}
              href={stat.href}
              className="group card-brilliant flex items-center justify-between bg-white p-6 transition-colors hover:border-secondary"
            >
              <div className="flex items-center gap-4">
                <div className="flex h-11 w-11 items-center justify-center bg-neutral-light text-secondary">
                  <Icon className="h-5 w-5" />
                </div>
                <div>
                  <p className="font-headline text-2xl font-bold text-neutral">{stat.totalCount}</p>
                  <p className="font-headline text-xs font-semibold uppercase tracking-wide text-neutral/60">
                    {stat.label}
                  </p>
                </div>
              </div>
              <ArrowRight className="h-4 w-4 text-neutral/40 transition-transform group-hover:translate-x-1 group-hover:text-secondary" />
            </Link>
          );
        })}
      </section>

      <div className="grid gap-4 lg:grid-cols-3">
        <div className="card-brilliant bg-white p-6 lg:col-span-2">
          <div className="flex items-center justify-between">
            <h2 className="font-headline text-lg font-bold uppercase">{t("quickActions")}</h2>
            <span className="text-xs text-neutral/50">{t("page", { page: pageIndex })}</span>
          </div>
          <div className="mt-5 grid gap-3 sm:grid-cols-2">
            {[
              { label: t("newService"), href: "/admin/services/new" },
              { label: t("newProject"), href: "/admin/projects/new" },
              { label: t("newProduct"), href: "/admin/products/new" },
              { label: t("newBlogPost"), href: "/admin/blog/new" },
              { label: t("newTeamMember"), href: "/admin/team/new" },
              { label: t("newClient"), href: "/admin/clients/new" },
              { label: t("newTestimonial"), href: "/admin/testimonials/new" },
            ].map((action) => (
              <Link
                key={action.href}
                href={action.href}
                className="inline-flex items-center justify-between border border-line px-4 py-3 text-sm text-neutral transition-colors hover:border-secondary hover:bg-neutral-light"
              >
                {action.label}
                <ArrowRight className="h-4 w-4 text-neutral/40" />
              </Link>
            ))}
          </div>
        </div>

        <div className="card-brilliant bg-neutral p-6 text-white">
          <h2 className="font-headline text-lg font-bold uppercase">{t("contactInfo")}</h2>
          <span className="mt-2 block h-[3px] w-8 bg-tertiary" aria-hidden="true" />
          <dl className="mt-5 space-y-3 text-sm">
            <div>
              <dt className="text-white/50">{t("phone")}</dt>
              <dd className="mt-0.5">{contact?.phone1 || "—"}</dd>
            </div>
            <div>
              <dt className="text-white/50">{t("email")}</dt>
              <dd className="mt-0.5">{contact?.email || "—"}</dd>
            </div>
            <div>
              <dt className="text-white/50">{t("address")}</dt>
              <dd className="mt-0.5 leading-relaxed">{contact?.address || "—"}</dd>
            </div>
          </dl>
          <Link
            href="/admin/settings"
            className="mt-6 inline-flex items-center gap-2 border border-white/25 px-4 py-2 font-headline text-xs font-semibold uppercase tracking-wide transition-colors hover:border-tertiary hover:text-tertiary"
          >
            {t("editContact")}
            <ArrowRight className="h-3.5 w-3.5" />
          </Link>
        </div>
      </div>

      {messageCount > 0 && (
        <p className="text-xs text-neutral/50">
          {t("submissionsNote")}
        </p>
      )}

      <div className="flex items-center justify-end gap-2 text-xs text-neutral/50">
        <Settings className="h-3.5 w-3.5" />
        <span>{t("settingsNote")}</span>
      </div>
    </div>
  );
}
