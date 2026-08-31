using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.ServiceCategories.Queries.GetServiceCategoryById;

public record GetServiceCategoryByIdQuery(int Id) : IRequest<ServiceCategoryDto>;
