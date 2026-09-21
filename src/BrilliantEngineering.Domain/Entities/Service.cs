using BrilliantEngineering.Domain.Common;

namespace BrilliantEngineering.Domain.Entities;

public class Service : BaseEntity
{
    public string Title { get; set; } = string.Empty;
    public string? TitleAr { get; set; }
    public string Description { get; set; } = string.Empty;
    public string? DescriptionAr { get; set; }
    public string IconPath { get; set; } = string.Empty;
    public int CategoryId { get; set; }
    public ServiceCategory? Category { get; set; }
    public int? RelatedBlogPostId { get; set; }
    public BlogPost? RelatedBlogPost { get; set; }
    public int Order { get; set; }
    public bool IsActive { get; set; } = true;
}
