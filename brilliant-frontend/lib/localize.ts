export function localized(
  locale: string,
  english: string,
  arabic?: string | null,
): string {
  return locale === "ar" ? (arabic && arabic.trim() ? arabic : english) : english;
}
