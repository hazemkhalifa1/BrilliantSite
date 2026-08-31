using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Models;
using MediatR;

namespace BrilliantEngineering.Application.Features.Projects.Queries.GetAllProjects;

public record GetAllProjectsQuery(int? TypeId, bool? OnlyActive, int PageIndex, int PageSize)
    : IRequest<PagedResult<ProjectDto>>;
