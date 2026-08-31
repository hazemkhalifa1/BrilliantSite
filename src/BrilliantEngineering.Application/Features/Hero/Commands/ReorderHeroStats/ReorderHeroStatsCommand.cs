using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.Hero.Commands.ReorderHeroStats;

public record ReorderHeroStatsCommand(IReadOnlyList<ReorderItemDto> Items) : IRequest<bool>;
