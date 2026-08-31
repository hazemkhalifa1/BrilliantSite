using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.Team.Commands.ReorderTeamMembers;

public record ReorderTeamMembersCommand(IReadOnlyList<ReorderItemDto> Items) : IRequest<bool>;
