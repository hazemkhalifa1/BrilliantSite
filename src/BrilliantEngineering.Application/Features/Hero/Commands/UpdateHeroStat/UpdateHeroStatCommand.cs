using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.Hero.Commands.UpdateHeroStat;

public record UpdateHeroStatCommand(
    int Id,
    string Value,
    string Label,
    string? LabelAr,
    bool IsActive) : IRequest<HeroStatDto>;
