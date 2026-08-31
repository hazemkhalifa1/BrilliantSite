using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.ProductCategories.Commands.CreateProductCategory;

public record CreateProductCategoryCommand(
    string Name,
    string? NameAr,
    int BrandId,
    bool IsActive) : IRequest<ProductCategoryDto>;
