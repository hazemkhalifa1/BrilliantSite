using BrilliantEngineering.Application.Common.Exceptions;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Team.Commands.DeleteTeamMember;

public class DeleteTeamMemberCommandHandler : IRequestHandler<DeleteTeamMemberCommand, bool>
{
    private readonly IUnitOfWork _unitOfWork;

    public DeleteTeamMemberCommandHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<bool> Handle(DeleteTeamMemberCommand request, CancellationToken cancellationToken)
    {
        var member = await _unitOfWork.Repository<TeamMember>().GetByIdAsync(request.Id);
        if (member is null)
            throw new NotFoundException(nameof(TeamMember), request.Id);

        _unitOfWork.Repository<TeamMember>().Delete(member);
        return await _unitOfWork.Complete() > 0;
    }
}
