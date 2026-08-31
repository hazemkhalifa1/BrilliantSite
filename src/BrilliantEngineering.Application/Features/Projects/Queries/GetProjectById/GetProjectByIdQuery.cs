using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.Projects.Queries.GetProjectById;

public record GetProjectByIdQuery(int Id) : IRequest<ProjectDto>;
