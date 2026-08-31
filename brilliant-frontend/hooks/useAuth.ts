import { useCallback, useEffect, useState } from "react";
import { removeToken, setToken } from "@/lib/api";
import type { AuthResponse, LoginRequest } from "@/types";
import { login as apiLogin } from "@/lib/auth";

export function useAuth() {
  const [user, setUser] = useState<AuthResponse | null>(null);
  const [loading, setLoading] = useState(false);

  useEffect(() => {
    const stored = localStorage.getItem("user");
    if (stored) {
      try {
        setUser(JSON.parse(stored));
      } catch {
        // ignore corrupt payload
      }
    }
  }, []);

  const isAuthenticated = useCallback(() => Boolean(localStorage.getItem("token")), []);

  const signIn = useCallback(async (credentials: LoginRequest) => {
    setLoading(true);
    try {
      const response = await apiLogin(credentials);
      setToken(response.token);
      localStorage.setItem("user", JSON.stringify(response));
      setUser(response);
      return response;
    } finally {
      setLoading(false);
    }
  }, []);

  const signOut = useCallback(() => {
    removeToken();
    localStorage.removeItem("user");
    setUser(null);
  }, []);

  return { user, loading, isAuthenticated, signIn, signOut };
}
