import { PageTransition } from "@/components/public/PageTransition";

export default function PublicTemplate({ children }: { children: React.ReactNode }) {
  return <PageTransition>{children}</PageTransition>;
}