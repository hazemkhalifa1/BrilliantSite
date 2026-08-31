using BrilliantEngineering.Domain.Common;
using BrilliantEngineering.Domain.Entities;

namespace BrilliantEngineering.Application.Common.Specifications;

public class ServicesWithCategorySpec : BaseSpecification<Service>
{
    public ServicesWithCategorySpec(int? categoryId, bool? onlyActive, string? search)
    {
        AddInclude(s => s.Category!);
        ApplyOrderBy(s => s.Order);

        if (categoryId.HasValue)
            ApplyCriteria(s => s.CategoryId == categoryId.Value);

        if (onlyActive == true)
            ApplyCriteria(s => s.IsActive);

        if (!string.IsNullOrWhiteSpace(search))
        {
            var term = search.Trim();
            ApplyCriteria(s => s.Title.Contains(term) || s.Description.Contains(term));
        }
    }
}

public class PagedServicesWithCategorySpec : ServicesWithCategorySpec
{
    public PagedServicesWithCategorySpec(int? categoryId, bool? onlyActive, string? search, int pageIndex, int pageSize)
        : base(categoryId, onlyActive, search)
    {
        ApplyPaging((pageIndex - 1) * pageSize, pageSize);
    }
}

public class ServiceByIdWithCategorySpec : BaseSpecification<Service>
{
    public ServiceByIdWithCategorySpec(int id)
    {
        AddInclude(s => s.Category!);
        ApplyCriteria(s => s.Id == id);
    }
}
