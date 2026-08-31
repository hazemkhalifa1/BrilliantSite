using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Application.Common.Exceptions;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Hero.Queries.GetHeroStatById;

public class GetHeroStatByIdQueryHandler : IRequestHandler<GetHeroStatByIdQuery, HeroStatDto>
{
    private readonly IUnitOfWork _unitOfWork;

    public GetHeroStatByIdQueryHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<HeroStatDto> Handle(GetHeroStatByIdQuery request, CancellationToken cancellationToken)
    {
        var stat = await _unitOfWork.Repository<HeroStat>().GetByIdAsync(request.Id);
        if (stat is null)
            throw new NotFoundException(nameof(HeroStat), request.Id);

        return stat.ToDto();
    }
}
