using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.Team.Queries.GetTeamMemberById;

public record GetTeamMemberByIdQuery(int Id) : IRequest<TeamMemberDto>;
