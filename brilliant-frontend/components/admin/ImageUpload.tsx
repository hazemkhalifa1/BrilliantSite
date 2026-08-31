"use client";

import { useRef, useState } from "react";
import Image from "next/image";
import { Loader2, Upload, X } from "lucide-react";
import { uploadImage } from "@/lib/upload";
import { getImageUrl } from "@/lib/utils";

export interface ImageUploadProps {
  entity: string;
  value?: string | null;
  onChange: (path: string | null) => void;
  label?: string;
}

export function ImageUpload({ entity, value, onChange, label }: ImageUploadProps) {
  const inputRef = useRef<HTMLInputElement>(null);
  const [uploading, setUploading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  async function handleFile(file?: File) {
    if (!file) return;
    setError(null);
    setUploading(true);
    try {
      const path = await uploadImage(entity, file);
      onChange(path);
    } catch (err) {
      setError(err instanceof Error ? err.message : "Upload failed");
    } finally {
      setUploading(false);
      if (inputRef.current) inputRef.current.value = "";
    }
  }

  return (
    <div className="flex flex-col gap-2">
      {label && <label className="text-sm font-medium text-neutral">{label}</label>}
      <div className="flex items-center gap-4">
        {value ? (
          <div className="relative h-24 w-32 overflow-hidden border border-line bg-white">
            <Image
              src={getImageUrl(value)}
              alt={label || "Uploaded image"}
              fill
              sizes="128px"
              className="object-contain"
            />
            <button
              type="button"
              onClick={() => onChange(null)}
              aria-label="Remove image"
              className="absolute end-1 top-1 flex h-6 w-6 items-center justify-center bg-tertiary text-white transition-colors hover:opacity-90"
            >
              <X className="h-4 w-4" />
            </button>
          </div>
        ) : (
          <div className="flex h-24 w-32 items-center justify-center border border-dashed border-line bg-neutral-light text-center text-xs text-neutral/50">
            No image
          </div>
        )}
        <button
          type="button"
          onClick={() => inputRef.current?.click()}
          disabled={uploading}
          className="inline-flex items-center gap-2 border border-neutral px-4 py-2 font-headline text-xs font-semibold uppercase tracking-wide text-neutral transition-colors hover:bg-neutral hover:text-white disabled:cursor-not-allowed disabled:opacity-50"
        >
          {uploading ? <Loader2 className="h-4 w-4 animate-spin" /> : <Upload className="h-4 w-4" />}
          {uploading ? "Uploading…" : value ? "Replace" : "Upload Image"}
        </button>
        <input
          ref={inputRef}
          type="file"
          accept="image/jpeg,image/png,image/webp"
          className="hidden"
          onChange={(event) => handleFile(event.target.files?.[0])}
        />
      </div>
      <div className="flex items-center gap-2">
        <span className="shrink-0 text-xs text-neutral/50">Or paste a URL:</span>
        <input
          type="text"
          value={value ?? ""}
          onChange={(event) => onChange(event.target.value.trim() || null)}
          placeholder="https://example.com/image.jpg"
          className="w-full border border-line bg-white px-3 py-2 text-sm text-neutral placeholder:text-neutral/40 focus:border-secondary focus:outline-none"
        />
      </div>
      {error && <p className="text-xs text-tertiary">{error}</p>}
    </div>
  );
}
