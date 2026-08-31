using MediatR;

namespace BrilliantEngineering.Application.Features.Team.Commands.DeleteTeamMember;

public record DeleteTeamMemberCommand(int Id) : IRequest<bool>;
