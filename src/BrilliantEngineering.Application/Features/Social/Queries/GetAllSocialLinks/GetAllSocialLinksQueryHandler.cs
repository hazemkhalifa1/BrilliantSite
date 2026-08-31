using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Application.Common.Models;
using BrilliantEngineering.Application.Common.Specifications;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Social.Queries.GetAllSocialLinks;

public class GetAllSocialLinksQueryHandler : IRequestHandler<GetAllSocialLinksQuery, PagedResult<SocialLinkDto>>
{
    private readonly IUnitOfWork _unitOfWork;

    public GetAllSocialLinksQueryHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<PagedResult<SocialLinkDto>> Handle(GetAllSocialLinksQuery request, CancellationToken cancellationToken)
    {
        var repository = _unitOfWork.Repository<SocialLink>();

        var spec = new PagedSocialLinksOrderedSpec(request.OnlyActive, request.PageIndex, request.PageSize);
        var countSpec = new SocialLinksOrderedSpec(request.OnlyActive);

        var links = await repository.GetAllWithSpecAsync(spec);
        var totalCount = await repository.CountAsync(countSpec);

        var items = links.ToDtoList();

        return PagedResult<SocialLinkDto>.Create(items, totalCount, request.PageIndex, request.PageSize);
    }
}
