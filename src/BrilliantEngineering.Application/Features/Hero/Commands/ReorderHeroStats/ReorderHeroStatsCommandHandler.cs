using BrilliantEngineering.Application.Common.Exceptions;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Hero.Commands.ReorderHeroStats;

public class ReorderHeroStatsCommandHandler : IRequestHandler<ReorderHeroStatsCommand, bool>
{
    private readonly IUnitOfWork _unitOfWork;

    public ReorderHeroStatsCommandHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<bool> Handle(ReorderHeroStatsCommand request, CancellationToken cancellationToken)
    {
        var repository = _unitOfWork.Repository<HeroStat>();
        var stats = await repository.GetAllAsync();
        var byId = stats.ToDictionary(s => s.Id);

        foreach (var item in request.Items)
        {
            if (!byId.TryGetValue(item.Id, out var stat))
                throw new NotFoundException(nameof(HeroStat), item.Id);

            stat.Order = item.Order;
            repository.Update(stat);
        }

        return await _unitOfWork.Complete() > 0;
    }
}
