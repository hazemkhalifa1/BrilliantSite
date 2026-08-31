import { API_BASE, getToken } from "@/lib/api";

async function upload(base: string, file: File): Promise<string> {
  const token = getToken();
  const formData = new FormData();
  formData.append("file", file);

  const res = await fetch(`${API_BASE}${base}`, {
    method: "POST",
    headers: token ? { Authorization: `Bearer ${token}` } : undefined,
    body: formData,
  });

  const body = await res.json().catch(() => null);
  if (!res.ok) {
    throw new Error(body?.message || `Upload failed (${res.status})`);
  }
  return body?.data as string;
}

export function uploadImage(entity: string, file: File): Promise<string> {
  return upload(`/upload/image/${entity}`, file);
}

export function uploadDocument(file: File): Promise<string> {
  return upload("/upload/document", file);
}
