import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { getTranslations } from "next-intl/server";
import { adminFetch } from "@/lib/adminApi";
import type { Testimonial } from "@/types";
import { TestimonialForm } from "../TestimonialForm";

export const dynamic = "force-dynamic";

export async function generateMetadata({
  params: { locale },
}: {
  params: { locale: string };
}): Promise<Metadata> {
  const form = await getTranslations({ locale, namespace: "admin.form" });
  const section = await getTranslations({ locale, namespace: "admin.sections.testimonials" });
  return { title: form("editTitle", { entity: section("entityTitle") }) };
}

interface EditTestimonialPageProps {
  params: { id: string };
}

export default async function EditTestimonialPage({ params }: EditTestimonialPageProps) {
  const id = Number(params.id);
  if (Number.isNaN(id)) notFound();

  const testimonial = await adminFetch<Testimonial>(`/testimonials/${id}`).catch(() => null);
  if (!testimonial) notFound();

  return <TestimonialForm initial={testimonial} />;
}