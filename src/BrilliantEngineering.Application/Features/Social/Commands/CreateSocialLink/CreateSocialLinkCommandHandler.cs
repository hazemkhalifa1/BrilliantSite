using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Social.Commands.CreateSocialLink;

public class CreateSocialLinkCommandHandler : IRequestHandler<CreateSocialLinkCommand, SocialLinkDto>
{
    private readonly IUnitOfWork _unitOfWork;

    public CreateSocialLinkCommandHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<SocialLinkDto> Handle(CreateSocialLinkCommand request, CancellationToken cancellationToken)
    {
        var links = await _unitOfWork.Repository<SocialLink>().GetAllAsync();
        var nextOrder = links.Count == 0 ? 0 : links.Max(l => l.Order) + 1;

        var link = new SocialLink
        {
            Platform = request.Platform,
            Url = request.Url,
            IconClass = request.IconClass,
            Order = nextOrder,
            IsActive = request.IsActive,
        };

        _unitOfWork.Repository<SocialLink>().Add(link);
        await _unitOfWork.Complete();

        return link.ToDto();
    }
}
