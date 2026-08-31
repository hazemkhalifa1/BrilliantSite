import * as React from "react";
import { cn } from "@/lib/utils";

export interface TextAreaProps extends React.TextareaHTMLAttributes<HTMLTextAreaElement> {
  label?: string;
  error?: string;
}

const TextArea = React.forwardRef<HTMLTextAreaElement, TextAreaProps>(
  ({ className, label, error, id, ...props }, ref) => {
    const textareaId = id || props.name || label;
    return (
      <div className="flex w-full flex-col gap-1.5">
        {label && (
          <label htmlFor={textareaId} className="text-sm font-medium text-neutral">
            {label}
          </label>
        )}
        <textarea
          ref={ref}
          id={textareaId}
          className={cn(
            "w-full resize-y border border-line bg-white px-3 py-2.5 text-sm text-neutral placeholder:text-neutral/40 focus:border-secondary focus:outline-none",
            error && "border-tertiary",
            className,
          )}
          {...props}
        />
        {error && <p className="text-xs text-tertiary">{error}</p>}
      </div>
    );
  },
);
TextArea.displayName = "TextArea";

export { TextArea };
