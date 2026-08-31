using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.Hero.Queries.GetHero;

public record GetHeroQuery : IRequest<HeroDto>;
