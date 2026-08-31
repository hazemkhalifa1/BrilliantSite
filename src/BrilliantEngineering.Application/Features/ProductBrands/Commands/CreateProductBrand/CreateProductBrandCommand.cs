using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.ProductBrands.Commands.CreateProductBrand;

public record CreateProductBrandCommand(
    string Name,
    string? NameAr,
    string Description,
    string? DescriptionAr,
    string BackgroundImagePath,
    bool IsActive) : IRequest<ProductBrandDto>;
