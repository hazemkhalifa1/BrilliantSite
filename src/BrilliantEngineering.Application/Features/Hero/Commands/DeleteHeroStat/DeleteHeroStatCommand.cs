using MediatR;

namespace BrilliantEngineering.Application.Features.Hero.Commands.DeleteHeroStat;

public record DeleteHeroStatCommand(int Id) : IRequest<bool>;
