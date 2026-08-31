using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Models;
using MediatR;

namespace BrilliantEngineering.Application.Features.ProductBrands.Queries.GetAllProductBrands;

public record GetAllProductBrandsQuery(bool? OnlyActive, int PageIndex, int PageSize) : IRequest<PagedResult<ProductBrandDto>>;
