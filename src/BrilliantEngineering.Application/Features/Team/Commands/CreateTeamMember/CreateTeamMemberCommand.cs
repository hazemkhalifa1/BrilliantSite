using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.Team.Commands.CreateTeamMember;

public record CreateTeamMemberCommand(
    string Name,
    string? NameAr,
    string JobTitle,
    string? JobTitleAr,
    string ImagePath,
    bool IsActive) : IRequest<TeamMemberDto>;
