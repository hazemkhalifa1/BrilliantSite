using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Models;
using MediatR;

namespace BrilliantEngineering.Application.Features.ServiceCategories.Queries.GetAllServiceCategories;

public record GetAllServiceCategoriesQuery(bool? OnlyActive, int PageIndex, int PageSize)
    : IRequest<PagedResult<ServiceCategoryDto>>;
