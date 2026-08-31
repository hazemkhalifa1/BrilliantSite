using BrilliantEngineering.Domain.Common;

namespace BrilliantEngineering.Domain.Entities;

public class Product : BaseEntity
{
    public string Name { get; set; } = string.Empty;
    public string? NameAr { get; set; }
    public string Description { get; set; } = string.Empty;
    public string? DescriptionAr { get; set; }
    public string ImagePath { get; set; } = string.Empty;
    public string DocumentationUrl { get; set; } = string.Empty;
    public int CategoryId { get; set; }
    public ProductCategory? Category { get; set; }
    public bool IsActive { get; set; } = true;
}
