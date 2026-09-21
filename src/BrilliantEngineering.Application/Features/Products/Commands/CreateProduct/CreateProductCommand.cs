using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.Products.Commands.CreateProduct;

public record CreateProductCommand(
    string Name,
    string? NameAr,
    string Description,
    string? DescriptionAr,
    string ImagePath,
    string DocumentationUrl,
    int CategoryId,
    bool IsActive,
    int? RelatedBlogPostId = null) : IRequest<ProductDto>;
