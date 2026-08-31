using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Application.Common.Exceptions;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Social.Queries.GetSocialLinkById;

public class GetSocialLinkByIdQueryHandler : IRequestHandler<GetSocialLinkByIdQuery, SocialLinkDto>
{
    private readonly IUnitOfWork _unitOfWork;

    public GetSocialLinkByIdQueryHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<SocialLinkDto> Handle(GetSocialLinkByIdQuery request, CancellationToken cancellationToken)
    {
        var link = await _unitOfWork.Repository<SocialLink>().GetByIdAsync(request.Id);
        if (link is null)
            throw new NotFoundException(nameof(SocialLink), request.Id);

        return link.ToDto();
    }
}
