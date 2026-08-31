using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.ProjectTypes.Commands.CreateProjectType;

public record CreateProjectTypeCommand(string Name, string? NameAr, bool IsActive) : IRequest<ProjectTypeDto>;
