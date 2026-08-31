using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Models;
using MediatR;

namespace BrilliantEngineering.Application.Features.ProjectTypes.Queries.GetAllProjectTypes;

public record GetAllProjectTypesQuery(bool? OnlyActive, int PageIndex, int PageSize)
    : IRequest<PagedResult<ProjectTypeDto>>;
