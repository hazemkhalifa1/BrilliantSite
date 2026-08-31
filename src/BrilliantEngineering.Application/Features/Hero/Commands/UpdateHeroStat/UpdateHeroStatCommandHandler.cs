using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Application.Common.Exceptions;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Hero.Commands.UpdateHeroStat;

public class UpdateHeroStatCommandHandler : IRequestHandler<UpdateHeroStatCommand, HeroStatDto>
{
    private readonly IUnitOfWork _unitOfWork;

    public UpdateHeroStatCommandHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<HeroStatDto> Handle(UpdateHeroStatCommand request, CancellationToken cancellationToken)
    {
        var stat = await _unitOfWork.Repository<HeroStat>().GetByIdAsync(request.Id);
        if (stat is null)
            throw new NotFoundException(nameof(HeroStat), request.Id);

        stat.Value = request.Value;
        stat.Label = request.Label;
        stat.LabelAr = request.LabelAr;
        stat.IsActive = request.IsActive;

        _unitOfWork.Repository<HeroStat>().Update(stat);
        await _unitOfWork.Complete();

        return stat.ToDto();
    }
}
