using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Application.Common.Models;
using BrilliantEngineering.Domain.Common;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.ServiceCategories.Queries.GetAllServiceCategories;

public class GetAllServiceCategoriesQueryHandler
    : IRequestHandler<GetAllServiceCategoriesQuery, PagedResult<ServiceCategoryDto>>
{
    private readonly IUnitOfWork _unitOfWork;

    public GetAllServiceCategoriesQueryHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<PagedResult<ServiceCategoryDto>> Handle(
        GetAllServiceCategoriesQuery request, CancellationToken cancellationToken)
    {
        var repository = _unitOfWork.Repository<ServiceCategory>();

        var listSpec = new ServiceCategoriesSpec(request.OnlyActive);
        var pageSpec = new PagedServiceCategoriesSpec(request.OnlyActive, request.PageIndex, request.PageSize);

        var categories = await repository.GetAllWithSpecAsync(pageSpec);
        var totalCount = await repository.CountAsync(listSpec);

        var items = categories.ToDtoList();

        return PagedResult<ServiceCategoryDto>.Create(items, totalCount, request.PageIndex, request.PageSize);
    }
}

internal class ServiceCategoriesSpec : BaseSpecification<ServiceCategory>
{
    public ServiceCategoriesSpec(bool? onlyActive)
    {
        ApplyOrderBy(c => c.Order);

        if (onlyActive == true)
            ApplyCriteria(c => c.IsActive);
    }
}

internal class PagedServiceCategoriesSpec : ServiceCategoriesSpec
{
    public PagedServiceCategoriesSpec(bool? onlyActive, int pageIndex, int pageSize)
        : base(onlyActive)
    {
        ApplyPaging((pageIndex - 1) * pageSize, pageSize);
    }
}
