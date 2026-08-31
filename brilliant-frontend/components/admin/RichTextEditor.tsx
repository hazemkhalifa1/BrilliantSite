"use client";

import dynamic from "next/dynamic";
import { cn } from "@/lib/utils";

const ReactQuill = dynamic(() => import("react-quill"), {
  ssr: false,
  loading: () => (
    <div className="flex h-44 items-center justify-center border border-line bg-neutral-light text-sm text-neutral/60">
      Loading editor…
    </div>
  ),
});

export interface RichTextEditorProps {
  value: string;
  onChange: (value: string) => void;
  label?: string;
  error?: string;
  placeholder?: string;
  className?: string;
}

const MODULES = {
  toolbar: [
    [{ header: [1, 2, 3, false] }],
    ["bold", "italic", "underline", "strike"],
    [{ list: "ordered" }, { list: "bullet" }],
    ["blockquote", "link", "image"],
    [{ align: [] }],
    ["clean"],
  ],
};

export function RichTextEditor({
  value,
  onChange,
  label,
  error,
  placeholder,
  className,
}: RichTextEditorProps) {
  return (
    <div className={cn("flex w-full flex-col gap-1.5", className)}>
      {label && <label className="text-sm font-medium text-neutral">{label}</label>}
      <div className={cn("rich-editor", error && "rich-editor-error")}>
        <ReactQuill
          theme="snow"
          value={value}
          onChange={onChange}
          modules={MODULES}
          placeholder={placeholder}
        />
      </div>
      {error && <p className="text-xs text-tertiary">{error}</p>}
    </div>
  );
}
