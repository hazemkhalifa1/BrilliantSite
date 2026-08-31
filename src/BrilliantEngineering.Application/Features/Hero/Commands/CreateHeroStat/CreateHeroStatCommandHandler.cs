using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Hero.Commands.CreateHeroStat;

public class CreateHeroStatCommandHandler : IRequestHandler<CreateHeroStatCommand, HeroStatDto>
{
    private readonly IUnitOfWork _unitOfWork;

    public CreateHeroStatCommandHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<HeroStatDto> Handle(CreateHeroStatCommand request, CancellationToken cancellationToken)
    {
        var stats = await _unitOfWork.Repository<HeroStat>().GetAllAsync();
        var nextOrder = stats.Count == 0 ? 0 : stats.Max(s => s.Order) + 1;

        var stat = new HeroStat
        {
            Value = request.Value,
            Label = request.Label,
            LabelAr = request.LabelAr,
            Order = nextOrder,
            IsActive = request.IsActive,
        };

        _unitOfWork.Repository<HeroStat>().Add(stat);
        await _unitOfWork.Complete();

        return stat.ToDto();
    }
}
