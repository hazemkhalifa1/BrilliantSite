using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Models;
using MediatR;

namespace BrilliantEngineering.Application.Features.Team.Queries.GetAllTeamMembers;

public record GetAllTeamMembersQuery(bool? OnlyActive, int PageIndex, int PageSize)
    : IRequest<PagedResult<TeamMemberDto>>;
