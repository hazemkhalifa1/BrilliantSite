using BrilliantEngineering.Application.Common.Exceptions;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Hero.Commands.DeleteHeroStat;

public class DeleteHeroStatCommandHandler : IRequestHandler<DeleteHeroStatCommand, bool>
{
    private readonly IUnitOfWork _unitOfWork;

    public DeleteHeroStatCommandHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<bool> Handle(DeleteHeroStatCommand request, CancellationToken cancellationToken)
    {
        var stat = await _unitOfWork.Repository<HeroStat>().GetByIdAsync(request.Id);
        if (stat is null)
            throw new NotFoundException(nameof(HeroStat), request.Id);

        _unitOfWork.Repository<HeroStat>().Delete(stat);
        return await _unitOfWork.Complete() > 0;
    }
}
