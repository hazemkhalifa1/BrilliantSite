using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Models;
using MediatR;

namespace BrilliantEngineering.Application.Features.Products.Queries.GetAllProducts;

public record GetAllProductsQuery(
    int? BrandId,
    int? CategoryId,
    bool? OnlyActive,
    string? Search,
    int PageIndex,
    int PageSize) : IRequest<PagedResult<ProductDto>>;
