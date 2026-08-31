import { cookies } from "next/headers";
import { API_BASE } from "@/lib/api";
import type { ApiResponse } from "@/types";

export async function adminFetch<T>(endpoint: string, options?: RequestInit): Promise<T> {
  const token = cookies().get("token")?.value;

  const res = await fetch(`${API_BASE}${endpoint}`, {
    ...options,
    cache: "no-store",
    headers: {
      "Content-Type": "application/json",
      ...(token ? { Authorization: `Bearer ${token}` } : {}),
      ...options?.headers,
    },
  });

  let body: ApiResponse<T> | null = null;
  try {
    body = await res.json();
  } catch {
    // Non-JSON response body
  }

  if (!res.ok) {
    throw new Error(body?.message || `API Error: ${res.status}`);
  }

  if (body === null || body.data === undefined) {
    throw new Error("Unexpected API response");
  }

  return body.data as T;
}

export function adminFetchList<T>(endpoint: string): Promise<{ items: T[]; totalCount: number }> {
  return adminFetch<{ items: T[]; totalCount: number }>(endpoint);
}
