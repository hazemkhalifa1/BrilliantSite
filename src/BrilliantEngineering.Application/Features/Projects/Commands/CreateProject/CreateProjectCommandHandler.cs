using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Application.Common.Exceptions;
using BrilliantEngineering.Application.Common.Specifications;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Projects.Commands.CreateProject;

public class CreateProjectCommandHandler : IRequestHandler<CreateProjectCommand, ProjectDto>
{
    private readonly IUnitOfWork _unitOfWork;

    public CreateProjectCommandHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<ProjectDto> Handle(CreateProjectCommand request, CancellationToken cancellationToken)
    {
        var typeExists = await _unitOfWork.Repository<ProjectType>().GetByIdAsync(request.TypeId) is not null;
        if (!typeExists)
            throw new NotFoundException(nameof(ProjectType), request.TypeId);

        var project = new Project
        {
            Title = request.Title,
            TitleAr = request.TitleAr,
            Description = request.Description,
            DescriptionAr = request.DescriptionAr,
            ImagePath = request.ImagePath,
            ClientName = request.ClientName,
            ClientNameAr = request.ClientNameAr,
            Year = request.Year,
            TypeId = request.TypeId,
            Order = request.Order,
            IsActive = request.IsActive,
        };

        _unitOfWork.Repository<Project>().Add(project);
        await _unitOfWork.Complete();

        var created = await _unitOfWork.Repository<Project>()
            .GetEntityWithSpecAsync(new ProjectByIdWithTypeSpec(project.Id));

        return created!.ToDto();
    }
}
