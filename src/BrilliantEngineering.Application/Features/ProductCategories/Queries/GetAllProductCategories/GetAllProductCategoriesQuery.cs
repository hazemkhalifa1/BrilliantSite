using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Models;
using MediatR;

namespace BrilliantEngineering.Application.Features.ProductCategories.Queries.GetAllProductCategories;

public record GetAllProductCategoriesQuery(int? BrandId, bool? OnlyActive, int PageIndex, int PageSize)
    : IRequest<PagedResult<ProductCategoryDto>>;
