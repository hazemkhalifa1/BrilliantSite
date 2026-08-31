using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Application.Common.Exceptions;
using BrilliantEngineering.Domain.Common;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.ProductBrands.Queries.GetProductBrandById;

public class GetProductBrandByIdQueryHandler : IRequestHandler<GetProductBrandByIdQuery, ProductBrandDto>
{
    private readonly IUnitOfWork _unitOfWork;

    public GetProductBrandByIdQueryHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<ProductBrandDto> Handle(GetProductBrandByIdQuery request, CancellationToken cancellationToken)
    {
        var brand = await _unitOfWork.Repository<ProductBrand>()
            .GetEntityWithSpecAsync(new ProductBrandByIdSpec(request.Id));

        if (brand is null)
            throw new NotFoundException(nameof(ProductBrand), request.Id);

        return brand.ToDto();
    }
}

internal class ProductBrandByIdSpec : BaseSpecification<ProductBrand>
{
    public ProductBrandByIdSpec(int id)
    {
        AddInclude(b => b.Categories);
        ApplyCriteria(b => b.Id == id);
    }
}
