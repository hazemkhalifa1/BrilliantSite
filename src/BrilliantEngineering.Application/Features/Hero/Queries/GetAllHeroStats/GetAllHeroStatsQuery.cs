using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Models;
using MediatR;

namespace BrilliantEngineering.Application.Features.Hero.Queries.GetAllHeroStats;

public record GetAllHeroStatsQuery(bool? OnlyActive, int PageIndex, int PageSize)
    : IRequest<PagedResult<HeroStatDto>>;
