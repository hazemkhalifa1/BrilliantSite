using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Application.Common.Exceptions;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Social.Commands.UpdateSocialLink;

public class UpdateSocialLinkCommandHandler : IRequestHandler<UpdateSocialLinkCommand, SocialLinkDto>
{
    private readonly IUnitOfWork _unitOfWork;

    public UpdateSocialLinkCommandHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<SocialLinkDto> Handle(UpdateSocialLinkCommand request, CancellationToken cancellationToken)
    {
        var link = await _unitOfWork.Repository<SocialLink>().GetByIdAsync(request.Id);
        if (link is null)
            throw new NotFoundException(nameof(SocialLink), request.Id);

        link.Platform = request.Platform;
        link.Url = request.Url;
        link.IconClass = request.IconClass;
        link.IsActive = request.IsActive;

        _unitOfWork.Repository<SocialLink>().Update(link);
        await _unitOfWork.Complete();

        return link.ToDto();
    }
}
