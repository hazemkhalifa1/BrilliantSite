using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Application.Common.Exceptions;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Team.Queries.GetTeamMemberById;

public class GetTeamMemberByIdQueryHandler : IRequestHandler<GetTeamMemberByIdQuery, TeamMemberDto>
{
    private readonly IUnitOfWork _unitOfWork;

    public GetTeamMemberByIdQueryHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<TeamMemberDto> Handle(GetTeamMemberByIdQuery request, CancellationToken cancellationToken)
    {
        var member = await _unitOfWork.Repository<TeamMember>().GetByIdAsync(request.Id);
        if (member is null)
            throw new NotFoundException(nameof(TeamMember), request.Id);

        return member.ToDto();
    }
}
