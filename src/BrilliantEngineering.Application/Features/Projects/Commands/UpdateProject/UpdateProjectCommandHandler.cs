using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Application.Common.Exceptions;
using BrilliantEngineering.Application.Common.Specifications;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Projects.Commands.UpdateProject;

public class UpdateProjectCommandHandler : IRequestHandler<UpdateProjectCommand, ProjectDto>
{
    private readonly IUnitOfWork _unitOfWork;

    public UpdateProjectCommandHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<ProjectDto> Handle(UpdateProjectCommand request, CancellationToken cancellationToken)
    {
        var project = await _unitOfWork.Repository<Project>().GetByIdAsync(request.Id);
        if (project is null)
            throw new NotFoundException(nameof(Project), request.Id);

        var typeExists = await _unitOfWork.Repository<ProjectType>().GetByIdAsync(request.TypeId) is not null;
        if (!typeExists)
            throw new NotFoundException(nameof(ProjectType), request.TypeId);

        project.Title = request.Title;
        project.TitleAr = request.TitleAr;
        project.Description = request.Description;
        project.DescriptionAr = request.DescriptionAr;
        project.ImagePath = request.ImagePath;
        project.ClientName = request.ClientName;
        project.ClientNameAr = request.ClientNameAr;
        project.Year = request.Year;
        project.TypeId = request.TypeId;
        project.Order = request.Order;
        project.IsActive = request.IsActive;

        _unitOfWork.Repository<Project>().Update(project);
        await _unitOfWork.Complete();

        var updated = await _unitOfWork.Repository<Project>()
            .GetEntityWithSpecAsync(new ProjectByIdWithTypeSpec(project.Id));

        return updated!.ToDto();
    }
}
