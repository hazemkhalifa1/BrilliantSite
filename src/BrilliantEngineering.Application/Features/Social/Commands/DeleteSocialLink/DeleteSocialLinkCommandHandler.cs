using BrilliantEngineering.Application.Common.Exceptions;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Social.Commands.DeleteSocialLink;

public class DeleteSocialLinkCommandHandler : IRequestHandler<DeleteSocialLinkCommand, bool>
{
    private readonly IUnitOfWork _unitOfWork;

    public DeleteSocialLinkCommandHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<bool> Handle(DeleteSocialLinkCommand request, CancellationToken cancellationToken)
    {
        var link = await _unitOfWork.Repository<SocialLink>().GetByIdAsync(request.Id);
        if (link is null)
            throw new NotFoundException(nameof(SocialLink), request.Id);

        _unitOfWork.Repository<SocialLink>().Delete(link);
        return await _unitOfWork.Complete() > 0;
    }
}
