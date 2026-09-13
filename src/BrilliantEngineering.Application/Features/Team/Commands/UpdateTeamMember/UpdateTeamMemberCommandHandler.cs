using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Application.Common.Exceptions;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Team.Commands.UpdateTeamMember;

public class UpdateTeamMemberCommandHandler : IRequestHandler<UpdateTeamMemberCommand, TeamMemberDto>
{
    private readonly IUnitOfWork _unitOfWork;

    public UpdateTeamMemberCommandHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<TeamMemberDto> Handle(UpdateTeamMemberCommand request, CancellationToken cancellationToken)
    {
        var member = await _unitOfWork.Repository<TeamMember>().GetByIdAsync(request.Id);
        if (member is null)
            throw new NotFoundException(nameof(TeamMember), request.Id);

        member.Name = request.Name;
        member.NameAr = request.NameAr;
        member.JobTitle = request.JobTitle;
        member.JobTitleAr = request.JobTitleAr;
        member.Description = request.Description;
        member.DescriptionAr = request.DescriptionAr;
        member.ImagePath = request.ImagePath;
        member.IsActive = request.IsActive;

        _unitOfWork.Repository<TeamMember>().Update(member);
        await _unitOfWork.Complete();

        return member.ToDto();
    }
}
