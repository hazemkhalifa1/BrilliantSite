using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Application.Common.Exceptions;
using BrilliantEngineering.Application.Common.Specifications;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Products.Commands.UpdateProduct;

public class UpdateProductCommandHandler : IRequestHandler<UpdateProductCommand, ProductDto>
{
    private readonly IUnitOfWork _unitOfWork;

    public UpdateProductCommandHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<ProductDto> Handle(UpdateProductCommand request, CancellationToken cancellationToken)
    {
        var product = await _unitOfWork.Repository<Product>().GetByIdAsync(request.Id);
        if (product is null)
            throw new NotFoundException(nameof(Product), request.Id);

        var categoryExists = await _unitOfWork.Repository<ProductCategory>().GetByIdAsync(request.CategoryId) is not null;
        if (!categoryExists)
            throw new NotFoundException(nameof(ProductCategory), request.CategoryId);

        product.Name = request.Name;
        product.NameAr = request.NameAr;
        product.Description = request.Description;
        product.DescriptionAr = request.DescriptionAr;
        product.ImagePath = request.ImagePath;
        product.DocumentationUrl = request.DocumentationUrl;
        product.CategoryId = request.CategoryId;
        product.IsActive = request.IsActive;

        _unitOfWork.Repository<Product>().Update(product);
        await _unitOfWork.Complete();

        var updated = await _unitOfWork.Repository<Product>()
            .GetEntityWithSpecAsync(new ProductByIdWithCategoryBrandSpec(product.Id));

        return updated!.ToDto();
    }
}
