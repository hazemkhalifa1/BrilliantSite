using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.Hero.Commands.UpdateHero;

public record UpdateHeroCommand(
    string HeadlineTop,
    string? HeadlineTopAr,
    string HeadlineBottom,
    string? HeadlineBottomAr,
    string SubText,
    string? SubTextAr,
    string PrimaryBtnText,
    string? PrimaryBtnTextAr,
    string PrimaryBtnUrl,
    string SecondaryBtnText,
    string? SecondaryBtnTextAr,
    string SecondaryBtnUrl) : IRequest<HeroDto>;
