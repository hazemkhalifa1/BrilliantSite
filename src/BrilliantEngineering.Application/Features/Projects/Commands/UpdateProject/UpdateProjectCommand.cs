using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.Projects.Commands.UpdateProject;

public record UpdateProjectCommand(
    int Id,
    string Title,
    string? TitleAr,
    string Description,
    string? DescriptionAr,
    string ImagePath,
    string ClientName,
    string? ClientNameAr,
    int Year,
    int TypeId,
    int Order,
    bool IsActive) : IRequest<ProjectDto>;
