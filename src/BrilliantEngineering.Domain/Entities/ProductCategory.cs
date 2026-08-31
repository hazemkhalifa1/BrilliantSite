using BrilliantEngineering.Domain.Common;

namespace BrilliantEngineering.Domain.Entities;

public class ProductCategory : BaseEntity
{
    public string Name { get; set; } = string.Empty;
    public string? NameAr { get; set; }
    public int BrandId { get; set; }
    public ProductBrand? Brand { get; set; }
    public bool IsActive { get; set; } = true;
    public ICollection<Product> Products { get; set; } = new List<Product>();
}
