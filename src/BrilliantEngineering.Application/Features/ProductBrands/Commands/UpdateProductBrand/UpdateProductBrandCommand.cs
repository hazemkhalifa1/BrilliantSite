using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.ProductBrands.Commands.UpdateProductBrand;

public record UpdateProductBrandCommand(
    int Id,
    string Description,
    string? DescriptionAr,
    string Name,
    string? NameAr,
    string BackgroundImagePath,
    bool IsActive) : IRequest<ProductBrandDto>;
