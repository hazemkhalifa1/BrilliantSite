using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Application.Common.Models;
using BrilliantEngineering.Application.Common.Specifications;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Hero.Queries.GetAllHeroStats;

public class GetAllHeroStatsQueryHandler : IRequestHandler<GetAllHeroStatsQuery, PagedResult<HeroStatDto>>
{
    private readonly IUnitOfWork _unitOfWork;

    public GetAllHeroStatsQueryHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<PagedResult<HeroStatDto>> Handle(GetAllHeroStatsQuery request, CancellationToken cancellationToken)
    {
        var repository = _unitOfWork.Repository<HeroStat>();

        var spec = new PagedHeroStatsOrderedSpec(request.OnlyActive, request.PageIndex, request.PageSize);
        var countSpec = new HeroStatsOrderedSpec(request.OnlyActive);

        var stats = await repository.GetAllWithSpecAsync(spec);
        var totalCount = await repository.CountAsync(countSpec);

        var items = stats.ToDtoList();

        return PagedResult<HeroStatDto>.Create(items, totalCount, request.PageIndex, request.PageSize);
    }
}
