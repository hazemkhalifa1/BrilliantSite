using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Application.Common.Models;
using BrilliantEngineering.Domain.Common;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.ProductCategories.Queries.GetAllProductCategories;

public class GetAllProductCategoriesQueryHandler
    : IRequestHandler<GetAllProductCategoriesQuery, PagedResult<ProductCategoryDto>>
{
    private readonly IUnitOfWork _unitOfWork;

    public GetAllProductCategoriesQueryHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<PagedResult<ProductCategoryDto>> Handle(
        GetAllProductCategoriesQuery request, CancellationToken cancellationToken)
    {
        var repository = _unitOfWork.Repository<ProductCategory>();

        var listSpec = new ProductCategoriesSpec(request.BrandId, request.OnlyActive);
        var pageSpec = new PagedProductCategoriesSpec(request.BrandId, request.OnlyActive, request.PageIndex, request.PageSize);

        var categories = await repository.GetAllWithSpecAsync(pageSpec);
        var totalCount = await repository.CountAsync(listSpec);

        var items = categories.ToDtoList();

        return PagedResult<ProductCategoryDto>.Create(items, totalCount, request.PageIndex, request.PageSize);
    }
}

internal class ProductCategoriesSpec : BaseSpecification<ProductCategory>
{
    public ProductCategoriesSpec(int? brandId, bool? onlyActive)
    {
        AddInclude(c => c.Brand!);
        ApplyOrderBy(c => c.Name);

        if (brandId.HasValue)
            ApplyCriteria(c => c.BrandId == brandId.Value);

        if (onlyActive == true)
            ApplyCriteria(c => c.IsActive);
    }
}

internal class PagedProductCategoriesSpec : ProductCategoriesSpec
{
    public PagedProductCategoriesSpec(int? brandId, bool? onlyActive, int pageIndex, int pageSize)
        : base(brandId, onlyActive)
    {
        ApplyPaging((pageIndex - 1) * pageSize, pageSize);
    }
}
