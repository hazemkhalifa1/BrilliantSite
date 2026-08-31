using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Application.Common.Models;
using BrilliantEngineering.Application.Common.Specifications;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Products.Queries.GetAllProducts;

public class GetAllProductsQueryHandler : IRequestHandler<GetAllProductsQuery, PagedResult<ProductDto>>
{
    private readonly IUnitOfWork _unitOfWork;

    public GetAllProductsQueryHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<PagedResult<ProductDto>> Handle(GetAllProductsQuery request, CancellationToken cancellationToken)
    {
        var repository = _unitOfWork.Repository<Product>();

        var spec = new PagedProductsWithCategoryBrandSpec(
            request.BrandId, request.CategoryId, request.OnlyActive, request.Search, request.PageIndex, request.PageSize);

        var countSpec = new ProductsWithCategoryBrandSpec(request.BrandId, request.CategoryId, request.OnlyActive, request.Search);

        var products = await repository.GetAllWithSpecAsync(spec);
        var totalCount = await repository.CountAsync(countSpec);

        var items = products.ToDtoList();

        return PagedResult<ProductDto>.Create(items, totalCount, request.PageIndex, request.PageSize);
    }
}
