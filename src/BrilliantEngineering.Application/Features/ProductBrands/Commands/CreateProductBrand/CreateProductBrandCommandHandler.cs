using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Application.Common.Exceptions;
using BrilliantEngineering.Domain.Common;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.ProductBrands.Commands.CreateProductBrand;

public class CreateProductBrandCommandHandler : IRequestHandler<CreateProductBrandCommand, ProductBrandDto>
{
    private readonly IUnitOfWork _unitOfWork;

    public CreateProductBrandCommandHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<ProductBrandDto> Handle(CreateProductBrandCommand request, CancellationToken cancellationToken)
    {
        var brand = new ProductBrand
        {
            Name = request.Name,
            NameAr = request.NameAr,
            Description = request.Description,
            DescriptionAr = request.DescriptionAr,
            BackgroundImagePath = request.BackgroundImagePath,
            IsActive = request.IsActive,
        };

        _unitOfWork.Repository<ProductBrand>().Add(brand);
        await _unitOfWork.Complete();

        var created = await _unitOfWork.Repository<ProductBrand>()
            .GetEntityWithSpecAsync(new ProductBrandByIdSpec(brand.Id))
            ?? throw new NotFoundException(nameof(ProductBrand), brand.Id);

        return created!.ToDto();
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
