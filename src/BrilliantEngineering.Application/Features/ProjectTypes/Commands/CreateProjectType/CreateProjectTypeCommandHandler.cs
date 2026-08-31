using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.ProjectTypes.Commands.CreateProjectType;

public class CreateProjectTypeCommandHandler : IRequestHandler<CreateProjectTypeCommand, ProjectTypeDto>
{
    private readonly IUnitOfWork _unitOfWork;

    public CreateProjectTypeCommandHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<ProjectTypeDto> Handle(CreateProjectTypeCommand request, CancellationToken cancellationToken)
    {
        var type = new ProjectType
        {
            Name = request.Name,
            NameAr = request.NameAr,
            IsActive = request.IsActive,
        };

        _unitOfWork.Repository<ProjectType>().Add(type);
        await _unitOfWork.Complete();

        return type.ToDto();
    }
}
