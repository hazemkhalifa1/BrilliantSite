using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Application.Common.Exceptions;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.ProjectTypes.Queries.GetProjectTypeById;

public class GetProjectTypeByIdQueryHandler : IRequestHandler<GetProjectTypeByIdQuery, ProjectTypeDto>
{
    private readonly IUnitOfWork _unitOfWork;

    public GetProjectTypeByIdQueryHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<ProjectTypeDto> Handle(GetProjectTypeByIdQuery request, CancellationToken cancellationToken)
    {
        var type = await _unitOfWork.Repository<ProjectType>().GetByIdAsync(request.Id);
        if (type is null)
            throw new NotFoundException(nameof(ProjectType), request.Id);

        return type.ToDto();
    }
}
