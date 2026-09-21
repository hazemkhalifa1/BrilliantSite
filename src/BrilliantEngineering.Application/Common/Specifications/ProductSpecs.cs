using BrilliantEngineering.Domain.Common;
using BrilliantEngineering.Domain.Entities;

namespace BrilliantEngineering.Application.Common.Specifications;

public class ProductsWithCategoryBrandSpec : BaseSpecification<Product>
{
    public ProductsWithCategoryBrandSpec(int? brandId, int? categoryId, bool? onlyActive, string? search)
    {
        AddInclude(p => p.Category!.Brand!);
        ApplyOrderByDescending(p => p.Id);

        if (brandId.HasValue)
            ApplyCriteria(p => p.Category!.BrandId == brandId.Value);

        if (categoryId.HasValue)
            ApplyCriteria(p => p.CategoryId == categoryId.Value);

        if (onlyActive == true)
            ApplyCriteria(p => p.IsActive);

        if (!string.IsNullOrWhiteSpace(search))
        {
            var term = search.Trim();
            ApplyCriteria(p => p.Name.Contains(term) || p.Description.Contains(term));
        }
    }
}

public class PagedProductsWithCategoryBrandSpec : ProductsWithCategoryBrandSpec
{
    public PagedProductsWithCategoryBrandSpec(int? brandId, int? categoryId, bool? onlyActive, string? search, int pageIndex, int pageSize)
        : base(brandId, categoryId, onlyActive, search)
    {
        ApplyPaging((pageIndex - 1) * pageSize, pageSize);
    }
}

public class ProductByIdWithCategoryBrandSpec : BaseSpecification<Product>
{
    public ProductByIdWithCategoryBrandSpec(int id)
    {
        AddInclude(p => p.Category!.Brand!);
        AddInclude(p => p.RelatedBlogPost!);
        ApplyCriteria(p => p.Id == id);
    }
}
