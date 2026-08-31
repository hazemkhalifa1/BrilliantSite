using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Application.Common.Exceptions;
using BrilliantEngineering.Application.Common.Specifications;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Hero.Queries.GetHero;

public class GetHeroQueryHandler : IRequestHandler<GetHeroQuery, HeroDto>
{
    private readonly IUnitOfWork _unitOfWork;

    public GetHeroQueryHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<HeroDto> Handle(GetHeroQuery request, CancellationToken cancellationToken)
    {
        var hero = await _unitOfWork.Repository<HeroSection>()
            .GetEntityWithSpecAsync(new SingleHeroSpec());

        if (hero is null)
            throw new NotFoundException("Hero section was not found.");

        var stats = await _unitOfWork.Repository<HeroStat>()
            .GetAllWithSpecAsync(new HeroStatsOrderedSpec(onlyActive: true));

        var dto = hero.ToDto();
        return dto with { Stats = stats.ToDtoList() };
    }
}
