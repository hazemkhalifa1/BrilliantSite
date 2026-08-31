import type { Metadata } from "next";
import { adminFetchList } from "@/lib/adminApi";
import type { Tag } from "@/types";
import { BlogPostForm } from "../BlogPostForm";

export const dynamic = "force-dynamic";

export const metadata: Metadata = { title: "New Post" };

export default async function NewBlogPostPage() {
  const tags = await adminFetchList<Tag>("/tags?pageSize=100")
    .then((data) => data.items)
    .catch(() => [] as Tag[]);
  return <BlogPostForm tags={tags} />;
}
