using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.Hero.Queries.GetHeroStatById;

public record GetHeroStatByIdQuery(int Id) : IRequest<HeroStatDto>;
