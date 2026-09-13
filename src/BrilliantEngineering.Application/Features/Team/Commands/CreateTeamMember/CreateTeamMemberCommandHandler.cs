using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Team.Commands.CreateTeamMember;

public class CreateTeamMemberCommandHandler : IRequestHandler<CreateTeamMemberCommand, TeamMemberDto>
{
    private readonly IUnitOfWork _unitOfWork;

    public CreateTeamMemberCommandHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<TeamMemberDto> Handle(CreateTeamMemberCommand request, CancellationToken cancellationToken)
    {
        var members = await _unitOfWork.Repository<TeamMember>().GetAllAsync();
        var nextOrder = members.Count == 0 ? 0 : members.Max(m => m.Order) + 1;

        var member = new TeamMember
        {
            Name = request.Name,
            NameAr = request.NameAr,
            JobTitle = request.JobTitle,
            JobTitleAr = request.JobTitleAr,
            Description = request.Description,
            DescriptionAr = request.DescriptionAr,
            ImagePath = request.ImagePath,
            Order = nextOrder,
            IsActive = request.IsActive,
        };

        _unitOfWork.Repository<TeamMember>().Add(member);
        await _unitOfWork.Complete();

        return member.ToDto();
    }
}
