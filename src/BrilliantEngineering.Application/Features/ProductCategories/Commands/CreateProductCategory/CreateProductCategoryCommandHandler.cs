using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Application.Common.Exceptions;
using BrilliantEngineering.Domain.Common;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.ProductCategories.Commands.CreateProductCategory;

public class CreateProductCategoryCommandHandler : IRequestHandler<CreateProductCategoryCommand, ProductCategoryDto>
{
    private readonly IUnitOfWork _unitOfWork;

    public CreateProductCategoryCommandHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<ProductCategoryDto> Handle(CreateProductCategoryCommand request, CancellationToken cancellationToken)
    {
        var brandExists = await _unitOfWork.Repository<ProductBrand>().GetByIdAsync(request.BrandId) is not null;
        if (!brandExists)
            throw new NotFoundException(nameof(ProductBrand), request.BrandId);

        var category = new ProductCategory
        {
            Name = request.Name,
            NameAr = request.NameAr,
            BrandId = request.BrandId,
            IsActive = request.IsActive,
        };

        _unitOfWork.Repository<ProductCategory>().Add(category);
        await _unitOfWork.Complete();

        var created = await _unitOfWork.Repository<ProductCategory>()
            .GetEntityWithSpecAsync(new ProductCategoryByIdSpec(category.Id));

        return created!.ToDto();
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
