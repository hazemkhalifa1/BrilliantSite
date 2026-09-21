"use client";

import { useState } from "react";
import { Link } from "@/src/i18n/navigation";
import { usePathname, useRouter } from "@/src/i18n/navigation";
import { useTranslations } from "next-intl";
import {
  Building2,
  ExternalLink,
  FolderKanban,
  Handshake,
  LayoutDashboard,
  Layers,
  LogOut,
  Menu,
  Newspaper,
  Package,
  Settings,
  Stamp,
  Tags,
  Users,
  X,
  MessageSquareQuote,
} from "lucide-react";
import { useAuth } from "@/hooks/useAuth";
import { cn } from "@/lib/utils";
import { initials } from "@/lib/utils";

export function AdminShell({ children }: { children: React.ReactNode }) {
  const t = useTranslations("admin.shell");
  const pathname = usePathname();
  const router = useRouter();
  const { user, signOut } = useAuth();
  const [mobileOpen, setMobileOpen] = useState(false);

  const NAV_ITEMS = [
    { label: t("dashboard"), href: "/admin", icon: LayoutDashboard, exact: true },
    { label: t("services"), href: "/admin/services", icon: Building2 },
    { label: t("serviceCategories"), href: "/admin/service-categories", icon: Layers },
    { label: t("projects"), href: "/admin/projects", icon: FolderKanban },
    { label: t("products"), href: "/admin/products", icon: Package },
    { label: t("productBrands"), href: "/admin/product-brands", icon: Stamp },
    { label: t("productCategories"), href: "/admin/product-categories", icon: Tags },
    { label: t("blog"), href: "/admin/blog", icon: Newspaper },
    { label: t("team"), href: "/admin/team", icon: Users },
    { label: t("clients"), href: "/admin/clients", icon: Handshake },
    { label: t("testimonials"), href: "/admin/testimonials", icon: MessageSquareQuote },
    { label: t("settings"), href: "/admin/settings", icon: Settings },
  ];

  function handleSignOut() {
    document.cookie = "token=; path=/; max-age=0";
    signOut();
    router.push("/login");
  }

  const isActive = (href: string, exact?: boolean) =>
    exact ? pathname === href : pathname === href || pathname.startsWith(`${href}/`);

  const nav = (
    <nav className="flex flex-1 flex-col gap-1 px-3 py-4">
      {NAV_ITEMS.map((item) => {
        const Icon = item.icon;
        const active = isActive(item.href, item.exact);
        return (
          <Link
            key={item.href}
            href={item.href}
            onClick={() => setMobileOpen(false)}
            className={cn(
              "flex items-center gap-3 px-3 py-2.5 font-headline text-sm font-semibold uppercase tracking-wide transition-colors",
              active ? "bg-tertiary text-white" : "text-white/70 hover:bg-white/10 hover:text-white",
            )}
          >
            <Icon className="h-4 w-4 shrink-0" />
            {item.label}
          </Link>
        );
      })}
    </nav>
  );

  return (
    <div className="min-h-screen bg-neutral-light">
      <aside className="fixed inset-y-0 start-0 z-40 hidden w-60 flex-col bg-neutral text-white lg:flex">
        <div className="flex h-16 items-center border-b border-white/10 px-4">
          <Link href="/admin" className="font-headline text-lg font-bold uppercase tracking-wide">
            Brilliant<span className="text-tertiary">Admin</span>
          </Link>
        </div>
        {nav}
        <div className="border-t border-white/10 p-3">
          <button
            onClick={handleSignOut}
            className="flex w-full items-center gap-3 px-3 py-2.5 font-headline text-sm font-semibold uppercase tracking-wide text-white/70 transition-colors hover:bg-white/10 hover:text-white"
          >
            <LogOut className="h-4 w-4 shrink-0" />
            {t("signOut")}
          </button>
        </div>
      </aside>

      {mobileOpen && (
        <div className="fixed inset-0 z-50 lg:hidden">
          <div className="absolute inset-0 bg-black/60" onClick={() => setMobileOpen(false)} aria-hidden="true" />
          <aside className="absolute inset-y-0 start-0 flex w-64 flex-col bg-neutral text-white">
            <div className="flex h-16 items-center justify-between border-b border-white/10 px-4">
              <span className="font-headline text-lg font-bold uppercase tracking-wide">
                Brilliant<span className="text-tertiary">Admin</span>
              </span>
              <button onClick={() => setMobileOpen(false)} aria-label={t("closeMenu")} className="p-1">
                <X className="h-5 w-5" />
              </button>
            </div>
            {nav}
            <div className="border-t border-white/10 p-3">
              <button
                onClick={handleSignOut}
                className="flex w-full items-center gap-3 px-3 py-2.5 font-headline text-sm font-semibold uppercase tracking-wide text-white/70 transition-colors hover:bg-white/10 hover:text-white"
              >
                <LogOut className="h-4 w-4 shrink-0" />
                {t("signOut")}
              </button>
            </div>
          </aside>
        </div>
      )}

      <div className="lg:ps-60">
        <header className="sticky top-0 z-30 flex h-16 items-center justify-between border-b border-line bg-white px-4 sm:px-6">
          <div className="flex items-center gap-3">
            <button
              onClick={() => setMobileOpen(true)}
              aria-label={t("openMenu")}
              className="p-1.5 text-neutral hover:text-tertiary lg:hidden"
            >
              <Menu className="h-5 w-5" />
            </button>
            <Link
              href="/"
              target="_blank"
              className="inline-flex items-center gap-1.5 font-headline text-xs font-semibold uppercase tracking-wide text-neutral/60 transition-colors hover:text-secondary"
            >
              <ExternalLink className="h-4 w-4" />
              {t("viewSite")}
            </Link>
          </div>

          <div className="flex items-center gap-3">
            <div className="hidden text-end sm:block">
              <p className="font-headline text-sm font-semibold">{user?.fullName || user?.email || t("defaultUserName")}</p>
              {user?.email && user?.fullName && (
                <p className="text-xs text-neutral/60">{user.email}</p>
              )}
            </div>
            <div className="flex h-9 w-9 items-center justify-center rounded-full bg-secondary font-headline text-sm font-bold text-white">
              {initials(user?.fullName || user?.email)}
            </div>
          </div>
        </header>

        <main className="p-4 sm:p-6 lg:p-8">{children}</main>
      </div>
    </div>
  );
}
