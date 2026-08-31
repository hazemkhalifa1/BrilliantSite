using BrilliantEngineering.Domain.Common;
using BrilliantEngineering.Domain.Entities;

namespace BrilliantEngineering.Application.Common.Specifications;

public class ProjectsWithTypeSpec : BaseSpecification<Project>
{
    public ProjectsWithTypeSpec(int? typeId, bool? onlyActive)
    {
        AddInclude(p => p.Type!);
        ApplyOrderBy(p => p.Order);

        if (typeId.HasValue)
            ApplyCriteria(p => p.TypeId == typeId.Value);

        if (onlyActive == true)
            ApplyCriteria(p => p.IsActive);
    }
}

public class PagedProjectsWithTypeSpec : ProjectsWithTypeSpec
{
    public PagedProjectsWithTypeSpec(int? typeId, bool? onlyActive, int pageIndex, int pageSize)
        : base(typeId, onlyActive)
    {
        ApplyPaging((pageIndex - 1) * pageSize, pageSize);
    }
}

public class ProjectByIdWithTypeSpec : BaseSpecification<Project>
{
    public ProjectByIdWithTypeSpec(int id)
    {
        AddInclude(p => p.Type!);
        ApplyCriteria(p => p.Id == id);
    }
}
