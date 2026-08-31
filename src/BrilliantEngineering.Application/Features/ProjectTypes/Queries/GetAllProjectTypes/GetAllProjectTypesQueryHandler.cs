using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Application.Common.Models;
using BrilliantEngineering.Domain.Common;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.ProjectTypes.Queries.GetAllProjectTypes;

public class GetAllProjectTypesQueryHandler
    : IRequestHandler<GetAllProjectTypesQuery, PagedResult<ProjectTypeDto>>
{
    private readonly IUnitOfWork _unitOfWork;

    public GetAllProjectTypesQueryHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<PagedResult<ProjectTypeDto>> Handle(
        GetAllProjectTypesQuery request, CancellationToken cancellationToken)
    {
        var repository = _unitOfWork.Repository<ProjectType>();

        var listSpec = new ProjectTypesSpec(request.OnlyActive);
        var pageSpec = new PagedProjectTypesSpec(request.OnlyActive, request.PageIndex, request.PageSize);

        var types = await repository.GetAllWithSpecAsync(pageSpec);
        var totalCount = await repository.CountAsync(listSpec);

        var items = types.ToDtoList();

        return PagedResult<ProjectTypeDto>.Create(items, totalCount, request.PageIndex, request.PageSize);
    }
}

internal class ProjectTypesSpec : BaseSpecification<ProjectType>
{
    public ProjectTypesSpec(bool? onlyActive)
    {
        ApplyOrderBy(t => t.Name);

        if (onlyActive == true)
            ApplyCriteria(t => t.IsActive);
    }
}

internal class PagedProjectTypesSpec : ProjectTypesSpec
{
    public PagedProjectTypesSpec(bool? onlyActive, int pageIndex, int pageSize)
        : base(onlyActive)
    {
        ApplyPaging((pageIndex - 1) * pageSize, pageSize);
    }
}
