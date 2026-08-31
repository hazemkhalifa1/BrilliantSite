using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.ProductBrands.Queries.GetProductBrandById;

public record GetProductBrandByIdQuery(int Id) : IRequest<ProductBrandDto>;
