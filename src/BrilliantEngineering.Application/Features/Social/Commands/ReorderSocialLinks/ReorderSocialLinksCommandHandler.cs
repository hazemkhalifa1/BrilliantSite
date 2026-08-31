using BrilliantEngineering.Application.Common.Exceptions;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Social.Commands.ReorderSocialLinks;

public class ReorderSocialLinksCommandHandler : IRequestHandler<ReorderSocialLinksCommand, bool>
{
    private readonly IUnitOfWork _unitOfWork;

    public ReorderSocialLinksCommandHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<bool> Handle(ReorderSocialLinksCommand request, CancellationToken cancellationToken)
    {
        var repository = _unitOfWork.Repository<SocialLink>();
        var links = await repository.GetAllAsync();
        var byId = links.ToDictionary(l => l.Id);

        foreach (var item in request.Items)
        {
            if (!byId.TryGetValue(item.Id, out var link))
                throw new NotFoundException(nameof(SocialLink), item.Id);

            link.Order = item.Order;
            repository.Update(link);
        }

        return await _unitOfWork.Complete() > 0;
    }
}
