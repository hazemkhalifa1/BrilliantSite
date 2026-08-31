using BrilliantEngineering.Domain.Common;

namespace BrilliantEngineering.Domain.Entities;

public class BlogPost : BaseEntity
{
    public string Title { get; set; } = string.Empty;
    public string? TitleAr { get; set; }
    public string Content { get; set; } = string.Empty;
    public string? ContentAr { get; set; }
    public string CoverImagePath { get; set; } = string.Empty;
    public string Slug { get; set; } = string.Empty;
    public string MetaTitle { get; set; } = string.Empty;
    public string? MetaTitleAr { get; set; }
    public string MetaDescription { get; set; } = string.Empty;
    public string? MetaDescriptionAr { get; set; }
    public DateTime? PublishedAt { get; set; }
    public bool IsPublished { get; set; }
    public int Order { get; set; }
    public ICollection<BlogPostTag> BlogPostTags { get; set; } = new List<BlogPostTag>();
}
