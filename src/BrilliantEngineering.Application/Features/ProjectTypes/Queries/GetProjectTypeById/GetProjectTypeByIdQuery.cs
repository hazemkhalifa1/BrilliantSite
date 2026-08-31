using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.ProjectTypes.Queries.GetProjectTypeById;

public record GetProjectTypeByIdQuery(int Id) : IRequest<ProjectTypeDto>;
