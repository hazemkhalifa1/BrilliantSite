using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Application.Common.Exceptions;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.ProjectTypes.Commands.UpdateProjectType;

public class UpdateProjectTypeCommandHandler : IRequestHandler<UpdateProjectTypeCommand, ProjectTypeDto>
{
    private readonly IUnitOfWork _unitOfWork;

    public UpdateProjectTypeCommandHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<ProjectTypeDto> Handle(UpdateProjectTypeCommand request, CancellationToken cancellationToken)
    {
        var type = await _unitOfWork.Repository<ProjectType>().GetByIdAsync(request.Id);
        if (type is null)
            throw new NotFoundException(nameof(ProjectType), request.Id);

        type.Name = request.Name;
        type.NameAr = request.NameAr;
        type.IsActive = request.IsActive;

        _unitOfWork.Repository<ProjectType>().Update(type);
        await _unitOfWork.Complete();

        return type.ToDto();
    }
}
