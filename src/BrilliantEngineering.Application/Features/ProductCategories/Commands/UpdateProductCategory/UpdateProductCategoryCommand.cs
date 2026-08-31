using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.ProductCategories.Commands.UpdateProductCategory;

public record UpdateProductCategoryCommand(
    int Id,
    string Name,
    string? NameAr,
    int BrandId,
    bool IsActive) : IRequest<ProductCategoryDto>;
