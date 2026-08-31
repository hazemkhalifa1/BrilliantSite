"use client";

import * as React from "react";
import { ChevronLeft, ChevronRight } from "lucide-react";
import { cn } from "@/lib/utils";

export interface PaginationProps {
  pageIndex: number;
  totalCount: number;
  pageSize: number;
  basePath: string;
  queryParams?: Record<string, string | number | undefined>;
}

export function Pagination({ pageIndex, totalCount, pageSize, basePath, queryParams }: PaginationProps) {
  const totalPages = Math.max(1, Math.ceil(totalCount / pageSize));

  const buildUrl = (page: number) => {
    const params = new URLSearchParams();
    if (queryParams) {
      for (const [key, value] of Object.entries(queryParams)) {
        if (value !== undefined && value !== null && value !== "") {
          params.set(key, String(value));
        }
      }
    }
    if (page > 1) params.set("page", String(page));
    const qs = params.toString();
    return qs ? `${basePath}?${qs}` : basePath;
  };

  const goToPage = (page: number) => {
    window.location.href = buildUrl(page);
  };

  const getPages = (): (number | "…")[] => {
    if (totalPages <= 7) {
      return Array.from({ length: totalPages }, (_, i) => i + 1);
    }
    const pages: (number | "…")[] = [1];
    const start = Math.max(2, pageIndex - 1);
    const end = Math.min(totalPages - 1, pageIndex + 1);
    if (start > 2) pages.push("…");
    for (let i = start; i <= end; i += 1) pages.push(i);
    if (end < totalPages - 1) pages.push("…");
    pages.push(totalPages);
    return pages;
  };

  if (totalPages <= 1) return null;

  return (
    <nav aria-label="Pagination" className="flex items-center justify-center gap-1">
      <button
        onClick={() => goToPage(pageIndex - 1)}
        disabled={pageIndex <= 1}
        aria-label="Previous page"
        className="flex h-9 w-9 items-center justify-center border border-line text-neutral transition-colors hover:bg-neutral-light disabled:cursor-not-allowed disabled:opacity-40"
      >
        <ChevronLeft className="h-4 w-4 rtl:rotate-180" />
      </button>

      {getPages().map((page, index) =>
        page === "…" ? (
          <span key={`ellipsis-${index}`} className="px-1 text-neutral/50">
            …
          </span>
        ) : (
          <button
            key={page}
            onClick={() => goToPage(page)}
            aria-current={page === pageIndex ? "page" : undefined}
            className={cn(
              "flex h-9 w-9 items-center justify-center border text-sm font-medium transition-colors",
              page === pageIndex
                ? "border-primary bg-primary text-white"
                : "border-line text-neutral hover:bg-neutral-light",
            )}
          >
            {page}
          </button>
        ),
      )}

      <button
        onClick={() => goToPage(pageIndex + 1)}
        disabled={pageIndex >= totalPages}
        aria-label="Next page"
        className="flex h-9 w-9 items-center justify-center border border-line text-neutral transition-colors hover:bg-neutral-light disabled:cursor-not-allowed disabled:opacity-40"
      >
        <ChevronRight className="h-4 w-4 rtl:rotate-180" />
      </button>
    </nav>
  );
}
