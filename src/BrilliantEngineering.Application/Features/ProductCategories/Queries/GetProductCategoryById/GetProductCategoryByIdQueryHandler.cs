using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Application.Common.Exceptions;
using BrilliantEngineering.Domain.Common;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.ProductCategories.Queries.GetProductCategoryById;

public class GetProductCategoryByIdQueryHandler : IRequestHandler<GetProductCategoryByIdQuery, ProductCategoryDto>
{
    private readonly IUnitOfWork _unitOfWork;

    public GetProductCategoryByIdQueryHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<ProductCategoryDto> Handle(GetProductCategoryByIdQuery request, CancellationToken cancellationToken)
    {
        var category = await _unitOfWork.Repository<ProductCategory>()
            .GetEntityWithSpecAsync(new ProductCategoryByIdSpec(request.Id));

        if (category is null)
            throw new NotFoundException(nameof(ProductCategory), request.Id);

        return category.ToDto();
    }
}

internal class ProductCategoryByIdSpec : BaseSpecification<ProductCategory>
{
    public ProductCategoryByIdSpec(int id)
    {
        AddInclude(c => c.Brand!);
        ApplyCriteria(c => c.Id == id);
    }
}
