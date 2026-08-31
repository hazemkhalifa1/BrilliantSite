using BrilliantEngineering.Application.Common.Exceptions;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Team.Commands.ReorderTeamMembers;

public class ReorderTeamMembersCommandHandler : IRequestHandler<ReorderTeamMembersCommand, bool>
{
    private readonly IUnitOfWork _unitOfWork;

    public ReorderTeamMembersCommandHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<bool> Handle(ReorderTeamMembersCommand request, CancellationToken cancellationToken)
    {
        var repository = _unitOfWork.Repository<TeamMember>();
        var members = await repository.GetAllAsync();
        var byId = members.ToDictionary(m => m.Id);

        foreach (var item in request.Items)
        {
            if (!byId.TryGetValue(item.Id, out var member))
                throw new NotFoundException(nameof(TeamMember), item.Id);

            member.Order = item.Order;
            repository.Update(member);
        }

        return await _unitOfWork.Complete() > 0;
    }
}
