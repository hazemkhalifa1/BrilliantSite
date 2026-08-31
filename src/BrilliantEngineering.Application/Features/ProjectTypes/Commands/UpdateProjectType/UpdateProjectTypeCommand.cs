using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.ProjectTypes.Commands.UpdateProjectType;

public record UpdateProjectTypeCommand(int Id, string Name, string? NameAr, bool IsActive) : IRequest<ProjectTypeDto>;
