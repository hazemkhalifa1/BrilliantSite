using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Application.Common.Exceptions;
using BrilliantEngineering.Application.Common.Specifications;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Products.Commands.CreateProduct;

public class CreateProductCommandHandler : IRequestHandler<CreateProductCommand, ProductDto>
{
    private readonly IUnitOfWork _unitOfWork;

    public CreateProductCommandHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<ProductDto> Handle(CreateProductCommand request, CancellationToken cancellationToken)
    {
        var categoryExists = await _unitOfWork.Repository<ProductCategory>().GetByIdAsync(request.CategoryId) is not null;
        if (!categoryExists)
            throw new NotFoundException(nameof(ProductCategory), request.CategoryId);

        var product = new Product
        {
            Name = request.Name,
            NameAr = request.NameAr,
            Description = request.Description,
            DescriptionAr = request.DescriptionAr,
            ImagePath = request.ImagePath,
            DocumentationUrl = request.DocumentationUrl,
            CategoryId = request.CategoryId,
            IsActive = request.IsActive,
        };

        _unitOfWork.Repository<Product>().Add(product);
        await _unitOfWork.Complete();

        var created = await _unitOfWork.Repository<Product>()
            .GetEntityWithSpecAsync(new ProductByIdWithCategoryBrandSpec(product.Id));

        return created!.ToDto();
    }
}
