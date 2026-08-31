using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Application.Common.Exceptions;
using BrilliantEngineering.Domain.Common;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.ProductBrands.Commands.UpdateProductBrand;

public class UpdateProductBrandCommandHandler : IRequestHandler<UpdateProductBrandCommand, ProductBrandDto>
{
    private readonly IUnitOfWork _unitOfWork;

    public UpdateProductBrandCommandHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<ProductBrandDto> Handle(UpdateProductBrandCommand request, CancellationToken cancellationToken)
    {
        var brand = await _unitOfWork.Repository<ProductBrand>().GetByIdAsync(request.Id);
        if (brand is null)
            throw new NotFoundException(nameof(ProductBrand), request.Id);

        brand.Name = request.Name;
        brand.NameAr = request.NameAr;
        brand.Description = request.Description;
        brand.DescriptionAr = request.DescriptionAr;
        brand.BackgroundImagePath = request.BackgroundImagePath;
        brand.IsActive = request.IsActive;

        _unitOfWork.Repository<ProductBrand>().Update(brand);
        await _unitOfWork.Complete();

        var updated = await _unitOfWork.Repository<ProductBrand>()
            .GetEntityWithSpecAsync(new ProductBrandByIdSpec(brand.Id));

        return updated!.ToDto();
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
