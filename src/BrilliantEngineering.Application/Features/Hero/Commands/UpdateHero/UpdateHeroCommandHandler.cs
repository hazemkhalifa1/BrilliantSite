using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Application.Common.Exceptions;
using BrilliantEngineering.Application.Common.Specifications;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Hero.Commands.UpdateHero;

public class UpdateHeroCommandHandler : IRequestHandler<UpdateHeroCommand, HeroDto>
{
    private readonly IUnitOfWork _unitOfWork;

    public UpdateHeroCommandHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<HeroDto> Handle(UpdateHeroCommand request, CancellationToken cancellationToken)
    {
        var hero = await _unitOfWork.Repository<HeroSection>()
            .GetEntityWithSpecAsync(new SingleHeroSpec())
            ?? throw new NotFoundException("Hero section was not found.");

        hero.HeadlineTop = request.HeadlineTop;
        hero.HeadlineTopAr = request.HeadlineTopAr;
        hero.HeadlineBottom = request.HeadlineBottom;
        hero.HeadlineBottomAr = request.HeadlineBottomAr;
        hero.SubText = request.SubText;
        hero.SubTextAr = request.SubTextAr;
        hero.PrimaryBtnText = request.PrimaryBtnText;
        hero.PrimaryBtnTextAr = request.PrimaryBtnTextAr;
        hero.PrimaryBtnUrl = request.PrimaryBtnUrl;
        hero.SecondaryBtnText = request.SecondaryBtnText;
        hero.SecondaryBtnTextAr = request.SecondaryBtnTextAr;
        hero.SecondaryBtnUrl = request.SecondaryBtnUrl;
        hero.UpdatedAt = DateTime.UtcNow;

        _unitOfWork.Repository<HeroSection>().Update(hero);
        await _unitOfWork.Complete();

        var stats = await _unitOfWork.Repository<HeroStat>()
            .GetAllWithSpecAsync(new HeroStatsOrderedSpec(onlyActive: true));

        var dto = hero.ToDto();
        return dto with { Stats = stats.ToDtoList() };
    }
}
