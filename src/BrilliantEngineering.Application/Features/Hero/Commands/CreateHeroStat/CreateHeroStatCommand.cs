using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.Hero.Commands.CreateHeroStat;

public record CreateHeroStatCommand(
    string Value,
    string Label,
    string? LabelAr,
    bool IsActive) : IRequest<HeroStatDto>;
