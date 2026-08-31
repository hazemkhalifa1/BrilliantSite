using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.ProductCategories.Queries.GetProductCategoryById;

public record GetProductCategoryByIdQuery(int Id) : IRequest<ProductCategoryDto>;
