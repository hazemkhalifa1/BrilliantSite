import type { Metadata } from "next";
import { notFound } from "next/navigation";
import { adminFetch, adminFetchList } from "@/lib/adminApi";
import type { BlogPost, Tag } from "@/types";
import { BlogPostForm } from "../BlogPostForm";

export const dynamic = "force-dynamic";

export const metadata: Metadata = { title: "Edit Post" };

interface EditBlogPostPageProps {
  params: { id: string };
}

export default async function EditBlogPostPage({ params }: EditBlogPostPageProps) {
  const id = Number(params.id);
  if (Number.isNaN(id)) notFound();

  const [post, tags] = await Promise.all([
    adminFetch<BlogPost>(`/blog/${id}`).catch(() => null),
    adminFetchList<Tag>("/tags?pageSize=100")
      .then((data) => data.items)
      .catch(() => [] as Tag[]),
  ]);

  if (!post) notFound();

  return <BlogPostForm initial={post} tags={tags} />;
}
