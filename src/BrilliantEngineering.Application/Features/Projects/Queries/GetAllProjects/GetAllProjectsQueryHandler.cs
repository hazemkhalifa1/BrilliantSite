using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Application.Common.Models;
using BrilliantEngineering.Application.Common.Specifications;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Projects.Queries.GetAllProjects;

public class GetAllProjectsQueryHandler : IRequestHandler<GetAllProjectsQuery, PagedResult<ProjectDto>>
{
    private readonly IUnitOfWork _unitOfWork;

    public GetAllProjectsQueryHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<PagedResult<ProjectDto>> Handle(GetAllProjectsQuery request, CancellationToken cancellationToken)
    {
        var repository = _unitOfWork.Repository<Project>();

        var spec = new PagedProjectsWithTypeSpec(request.TypeId, request.OnlyActive, request.PageIndex, request.PageSize);
        var countSpec = new ProjectsWithTypeSpec(request.TypeId, request.OnlyActive);

        var projects = await repository.GetAllWithSpecAsync(spec);
        var totalCount = await repository.CountAsync(countSpec);

        var items = projects.ToDtoList();

        return PagedResult<ProjectDto>.Create(items, totalCount, request.PageIndex, request.PageSize);
    }
}
