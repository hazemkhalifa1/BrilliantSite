import * as React from "react";
import { cn } from "@/lib/utils";

export interface Column<T> {
  key: string;
  header: string;
  cell: (row: T) => React.ReactNode;
  className?: string;
  headerClassName?: string;
}

export interface AdminTableProps<T> {
  columns: Column<T>[];
  data: T[];
  rowKey: (row: T) => string | number;
  emptyMessage?: string;
  actions?: (row: T) => React.ReactNode;
  actionsLabel?: string;
}

export function AdminTable<T>({
  columns,
  data,
  rowKey,
  emptyMessage = "No records found.",
  actions,
  actionsLabel = "Actions",
}: AdminTableProps<T>) {
  const colSpan = columns.length + (actions ? 1 : 0);
  return (
    <div className="overflow-x-auto border border-line bg-white">
      <table className="w-full min-w-max text-start text-sm">
        <thead>
          <tr className="border-b border-line bg-neutral-light">
            {columns.map((column) => (
              <th
                key={column.key}
                className={cn(
                  "px-4 py-3 font-headline text-xs font-semibold uppercase tracking-wide text-neutral",
                  column.headerClassName,
                )}
              >
                {column.header}
              </th>
            ))}
            {actions && (
              <th className="px-4 py-3 text-end font-headline text-xs font-semibold uppercase tracking-wide text-neutral">
                {actionsLabel}
              </th>
            )}
          </tr>
        </thead>
        <tbody>
          {data.length === 0 ? (
            <tr>
              <td colSpan={colSpan} className="px-4 py-12 text-center text-neutral/50">
                {emptyMessage}
              </td>
            </tr>
          ) : (
            data.map((row) => (
              <tr
                key={rowKey(row)}
                className="border-b border-line transition-colors last:border-b-0 hover:bg-neutral-light/60"
              >
                {columns.map((column) => (
                  <td
                    key={column.key}
                    className={cn("px-4 py-3 align-middle text-neutral/80", column.className)}
                  >
                    {column.cell(row)}
                  </td>
                ))}
                {actions && <td className="px-4 py-3 text-end">{actions(row)}</td>}
              </tr>
            ))
          )}
        </tbody>
      </table>
    </div>
  );
}
