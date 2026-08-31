using BrilliantEngineering.Domain.Common;
using BrilliantEngineering.Domain.Entities;

namespace BrilliantEngineering.Application.Common.Specifications;

public class BlogPostsWithTagsSpec : BaseSpecification<BlogPost>
{
    public BlogPostsWithTagsSpec(bool? publishedOnly, int? tagId)
    {
        AddInclude("BlogPostTags.Tag");
        ApplyOrderBy(p => p.Order);

        if (publishedOnly == true)
        {
            ApplyCriteria(p => p.IsPublished && p.PublishedAt != null);
        }

        if (tagId.HasValue)
            ApplyCriteria(p => p.BlogPostTags.Any(x => x.TagId == tagId.Value));
    }
}

public class PagedBlogPostsWithTagsSpec : BlogPostsWithTagsSpec
{
    public PagedBlogPostsWithTagsSpec(bool? publishedOnly, int? tagId, int pageIndex, int pageSize)
        : base(publishedOnly, tagId)
    {
        ApplyPaging((pageIndex - 1) * pageSize, pageSize);
    }
}

public class BlogPostByIdWithTagsSpec : BaseSpecification<BlogPost>
{
    public BlogPostByIdWithTagsSpec(int id)
    {
        AddInclude("BlogPostTags.Tag");
        ApplyCriteria(p => p.Id == id);
    }
}

public class BlogPostBySlugWithTagsSpec : BaseSpecification<BlogPost>
{
    public BlogPostBySlugWithTagsSpec(string slug, bool publishedOnly = false)
    {
        AddInclude("BlogPostTags.Tag");
        ApplyCriteria(p => p.Slug == slug);

        if (publishedOnly)
            ApplyCriteria(p => p.IsPublished && p.PublishedAt != null);
    }
}

public class TagSlugExistsSpec : BaseSpecification<Tag>
{
    public TagSlugExistsSpec(string slug, int excludeId = 0)
    {
        ApplyCriteria(t => t.Slug == slug && t.Id != excludeId);
    }
}
