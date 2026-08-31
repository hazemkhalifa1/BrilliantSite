using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.Team.Commands.UpdateTeamMember;

public record UpdateTeamMemberCommand(
    int Id,
    string Name,
    string? NameAr,
    string JobTitle,
    string? JobTitleAr,
    string ImagePath,
    bool IsActive) : IRequest<TeamMemberDto>;
