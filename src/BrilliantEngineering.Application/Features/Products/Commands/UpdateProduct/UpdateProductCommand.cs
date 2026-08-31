using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.Products.Commands.UpdateProduct;

public record UpdateProductCommand(
    int Id,
    string Name,
    string? NameAr,
    string Description,
    string? DescriptionAr,
    string ImagePath,
    string DocumentationUrl,
    int CategoryId,
    bool IsActive) : IRequest<ProductDto>;
