import type { ApiResponse } from "@/types";

export const API_BASE = process.env.NEXT_PUBLIC_API_URL || "http://localhost:5222/api";

export class ApiError extends Error {
  status: number;

  constructor(message: string, status: number) {
    super(message);
    this.name = "ApiError";
    this.status = status;
  }
}

export function getToken(): string | null {
  if (typeof window === "undefined") return null;
  return localStorage.getItem("token");
}

export async function apiFetch<T>(endpoint: string, options?: RequestInit): Promise<T> {
  const token = getToken();

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
    const message =
      body?.message ||
      (res.status === 401 ? "Unauthorized. Please sign in again." : `API Error: ${res.status}`);
    if (res.status === 401) {
      removeToken();
    }
    throw new ApiError(message, res.status);
  }

  if (body === null || body.data === undefined) {
    throw new ApiError("Unexpected API response", 502);
  }

  return body.data as T;
}

export function getTokenStorageKey(): string {
  return "token";
}

export function setToken(token: string): void {
  if (typeof window === "undefined") return;
  localStorage.setItem("token", token);
}

export function removeToken(): void {
  if (typeof window === "undefined") return;
  localStorage.removeItem("token");
}
