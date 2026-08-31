"use client";

import { useRef, useState } from "react";
import { FileText, Loader2, Upload, X } from "lucide-react";
import { uploadDocument } from "@/lib/upload";
import { getImageUrl } from "@/lib/utils";

export interface DocumentUploadProps {
  value?: string | null;
  onChange: (path: string | null) => void;
  label?: string;
}

export function DocumentUpload({ value, onChange, label }: DocumentUploadProps) {
  const inputRef = useRef<HTMLInputElement>(null);
  const [uploading, setUploading] = useState(false);
  const [error, setError] = useState<string | null>(null);

  async function handleFile(file?: File) {
    if (!file) return;
    setError(null);
    setUploading(true);
    try {
      const path = await uploadDocument(file);
      onChange(path);
    } catch (err) {
      setError(err instanceof Error ? err.message : "Upload failed");
    } finally {
      setUploading(false);
      if (inputRef.current) inputRef.current.value = "";
    }
  }

  const fileName = value ? value.split("/").pop() : null;

  return (
    <div className="flex flex-col gap-2">
      {label && <label className="text-sm font-medium text-neutral">{label}</label>}
      <div className="flex flex-wrap items-center gap-3">
        {value ? (
          <a
            href={getImageUrl(value)}
            target="_blank"
            rel="noopener noreferrer"
            className="inline-flex items-center gap-2 border border-line bg-neutral-light px-3 py-2 text-xs text-neutral transition-colors hover:border-secondary"
          >
            <FileText className="h-4 w-4 text-secondary" />
            {fileName}
          </a>
        ) : (
          <span className="inline-flex items-center gap-2 border border-dashed border-line px-3 py-2 text-xs text-neutral/50">
            <FileText className="h-4 w-4" />
            No document
          </span>
        )}
        <button
          type="button"
          onClick={() => inputRef.current?.click()}
          disabled={uploading}
          className="inline-flex items-center gap-2 border border-neutral px-4 py-2 font-headline text-xs font-semibold uppercase tracking-wide text-neutral transition-colors hover:bg-neutral hover:text-white disabled:cursor-not-allowed disabled:opacity-50"
        >
          {uploading ? <Loader2 className="h-4 w-4 animate-spin" /> : <Upload className="h-4 w-4" />}
          {uploading ? "Uploading…" : value ? "Replace" : "Upload Document"}
        </button>
        {value && (
          <button
            type="button"
            onClick={() => onChange(null)}
            aria-label="Remove document"
            className="inline-flex items-center gap-1 border border-line px-2 py-2 text-neutral/60 transition-colors hover:border-tertiary hover:text-tertiary"
          >
            <X className="h-4 w-4" />
          </button>
        )}
        <input
          ref={inputRef}
          type="file"
          accept=".pdf,.doc,.docx,.xls,.xlsx,.ppt,.pptx,.zip"
          className="hidden"
          onChange={(event) => handleFile(event.target.files?.[0])}
        />
      </div>
      <div className="flex items-center gap-2">
        <span className="shrink-0 text-xs text-neutral/50">Or paste a URL:</span>
        <input
          type="url"
          value={value ?? ""}
          onChange={(event) => onChange(event.target.value.trim() || null)}
          placeholder="https://example.com/catalog.pdf"
          className="w-full border border-line bg-white px-3 py-2 text-sm text-neutral placeholder:text-neutral/40 focus:border-secondary focus:outline-none"
        />
      </div>
      {error && <p className="text-xs text-tertiary">{error}</p>}
    </div>
  );
}
