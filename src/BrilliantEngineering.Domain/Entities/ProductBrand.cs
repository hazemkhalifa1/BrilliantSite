using BrilliantEngineering.Domain.Common;

namespace BrilliantEngineering.Domain.Entities;

public class ProductBrand : BaseEntity
{
    public string Name { get; set; } = string.Empty;
    public string? NameAr { get; set; }
    public string Description { get; set; } = string.Empty;
    public string? DescriptionAr { get; set; }
    public string BackgroundImagePath { get; set; } = string.Empty;
    public bool IsActive { get; set; } = true;
    public ICollection<ProductCategory> Categories { get; set; } = new List<ProductCategory>();
}
