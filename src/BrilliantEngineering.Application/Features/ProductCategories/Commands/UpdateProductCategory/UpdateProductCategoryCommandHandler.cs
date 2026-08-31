using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Application.Common.Exceptions;
using BrilliantEngineering.Domain.Common;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.ProductCategories.Commands.UpdateProductCategory;

public class UpdateProductCategoryCommandHandler : IRequestHandler<UpdateProductCategoryCommand, ProductCategoryDto>
{
    private readonly IUnitOfWork _unitOfWork;

    public UpdateProductCategoryCommandHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<ProductCategoryDto> Handle(UpdateProductCategoryCommand request, CancellationToken cancellationToken)
    {
        var category = await _unitOfWork.Repository<ProductCategory>().GetByIdAsync(request.Id);
        if (category is null)
            throw new NotFoundException(nameof(ProductCategory), request.Id);

        var brandExists = await _unitOfWork.Repository<ProductBrand>().GetByIdAsync(request.BrandId) is not null;
        if (!brandExists)
            throw new NotFoundException(nameof(ProductBrand), request.BrandId);

        category.Name = request.Name;
        category.NameAr = request.NameAr;
        category.BrandId = request.BrandId;
        category.IsActive = request.IsActive;

        _unitOfWork.Repository<ProductCategory>().Update(category);
        await _unitOfWork.Complete();

        var updated = await _unitOfWork.Repository<ProductCategory>()
            .GetEntityWithSpecAsync(new ProductCategoryByIdSpec(category.Id));

        return updated!.ToDto();
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
