using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Application.Common.Models;
using BrilliantEngineering.Domain.Common;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.ProductBrands.Queries.GetAllProductBrands;

public class GetAllProductBrandsQueryHandler : IRequestHandler<GetAllProductBrandsQuery, PagedResult<ProductBrandDto>>
{
    private readonly IUnitOfWork _unitOfWork;

    public GetAllProductBrandsQueryHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<PagedResult<ProductBrandDto>> Handle(GetAllProductBrandsQuery request, CancellationToken cancellationToken)
    {
        var repository = _unitOfWork.Repository<ProductBrand>();

        var listSpec = new ProductBrandsSpec(request.OnlyActive);
        var pageSpec = new PagedProductBrandsSpec(request.OnlyActive, request.PageIndex, request.PageSize);

        var brands = await repository.GetAllWithSpecAsync(pageSpec);
        var totalCount = await repository.CountAsync(listSpec);

        var items = brands.ToDtoList();

        return PagedResult<ProductBrandDto>.Create(items, totalCount, request.PageIndex, request.PageSize);
    }
}

internal class ProductBrandsSpec : BaseSpecification<ProductBrand>
{
    public ProductBrandsSpec(bool? onlyActive)
    {
        AddInclude(b => b.Categories);
        ApplyOrderBy(b => b.Name);

        if (onlyActive == true)
            ApplyCriteria(b => b.IsActive);
    }
}

internal class PagedProductBrandsSpec : ProductBrandsSpec
{
    public PagedProductBrandsSpec(bool? onlyActive, int pageIndex, int pageSize)
        : base(onlyActive)
    {
        ApplyPaging((pageIndex - 1) * pageSize, pageSize);
    }
}
