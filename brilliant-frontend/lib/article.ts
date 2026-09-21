import { marked } from "marked";

export function stripHtml(value: string): string {
  return value
    .replace(/<[^>]*>/g, " ")
    .replace(/&nbsp;/gi, " ")
    .replace(/\s+/g, " ")
    .trim();
}

function normalize(value: string): string {
  return value.replace(/\s+/g, " ").trim().toLowerCase();
}

const HTML_BLOCK_TAG = /<(?:p|h[1-6]|div|ul|ol|li|blockquote|pre|table|figure|img|hr)[\s>/]/i;

const MARKDOWN_SIGNALS = /(?:^|\n)\s{0,3}(?:#{1,6}\s|\*{3,}\s*$|[-*+]\s)|(?:^|\n)\s{0,3}\d+[.)]\s|(?:^|\n)\s{0,3}[-*_]{3,}\s*$|(?<!\*)__(?=\S)|(?<!\*)\*\*(?=\S)|(?<!\\)(?:^|[^\w])\*[^*\s][^*]*\*|[`]{1,3}/m;

/**
 * True when the stored content is already rich HTML (Quill editor output),
 * which must pass through unchanged instead of being parsed as Markdown.
 */
export function isHtmlContent(value: string): boolean {
  return HTML_BLOCK_TAG.test(value.trim().slice(0, 2000));
}

/**
 * Heuristic guard so plain HTML is never fed through the Markdown parser.
 */
export function looksLikeMarkdown(value: string): boolean {
  if (!value) return false;
  return !isHtmlContent(value) && MARKDOWN_SIGNALS.test(value);
}

/**
 * Converts Markdown into HTML. Used by cleanArticleHtml() so posts authored
 * as Markdown render with proper headings, bold, lists and horizontal rules.
 */
export function markdownToHtml(value: string): string {
  return marked.parse(value, { gfm: true, breaks: false }) as string;
}

const LEADING_EMPTY_BLOCK =
  /^(?:<(p|div)[^>]*>(?:\s|&nbsp;|<br\s*\/?>)*<\/\1>[ \t\r\n]*)+/i;

const FIRST_BLOCK = /^<([a-z][a-z0-9]*)[^>]*>([\s\S]*?)<\/\1>/i;

/**
 * Removes leading empty paragraphs and a leading paragraph/heading that
 * duplicates the article title. Every other paragraph is preserved verbatim.
 * Content stored as Markdown is converted to HTML before cleaning so the
 * article renders with full formatting (headings, bold, lists, rules).
 */
export function cleanArticleHtml(content: string, title: string): string {
  if (!content) return "";

  let html = content.trim();

  // Convert Markdown-authored posts to HTML. HTML content (Quill output)
  // is left untouched.
  if (looksLikeMarkdown(html)) {
    html = markdownToHtml(html).trim();
  }

  // Drop leading empty paragraphs left by the rich-text editor.
  html = html.replace(LEADING_EMPTY_BLOCK, "");

  // Drop a leading paragraph/heading that only repeats the article title.
  const first = FIRST_BLOCK.exec(html);
  if (first && normalize(stripHtml(first[2])) === normalize(title)) {
    html = html.slice(first[0].length).trim();
  }

  // Remove any leading empty paragraphs exposed by dropping the title.
  html = html.replace(LEADING_EMPTY_BLOCK, "");

  return html.trim();
}

export function readingMinutes(html: string): number {
  let text = html;
  if (looksLikeMarkdown(text)) {
    text = markdownToHtml(text);
  }
  const words = stripHtml(text).split(/\s+/).filter(Boolean).length;
  return Math.max(1, Math.round(words / 200));
}