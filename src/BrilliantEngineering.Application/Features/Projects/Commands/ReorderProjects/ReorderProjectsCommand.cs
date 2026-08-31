using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.Projects.Commands.ReorderProjects;

public record ReorderProjectsCommand(IReadOnlyList<ReorderItemDto> Items) : IRequest<bool>;
