using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Application.Common.Models;
using BrilliantEngineering.Application.Common.Specifications;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Team.Queries.GetAllTeamMembers;

public class GetAllTeamMembersQueryHandler : IRequestHandler<GetAllTeamMembersQuery, PagedResult<TeamMemberDto>>
{
    private readonly IUnitOfWork _unitOfWork;

    public GetAllTeamMembersQueryHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<PagedResult<TeamMemberDto>> Handle(GetAllTeamMembersQuery request, CancellationToken cancellationToken)
    {
        var repository = _unitOfWork.Repository<TeamMember>();

        var spec = new PagedTeamMembersOrderedSpec(request.OnlyActive, request.PageIndex, request.PageSize);
        var countSpec = new TeamMembersOrderedSpec(request.OnlyActive);

        var members = await repository.GetAllWithSpecAsync(spec);
        var totalCount = await repository.CountAsync(countSpec);

        var items = members.ToDtoList();

        return PagedResult<TeamMemberDto>.Create(items, totalCount, request.PageIndex, request.PageSize);
    }
}
