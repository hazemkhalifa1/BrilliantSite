using BrilliantEngineering.Application.Common.Exceptions;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Projects.Commands.ReorderProjects;

public class ReorderProjectsCommandHandler : IRequestHandler<ReorderProjectsCommand, bool>
{
    private readonly IUnitOfWork _unitOfWork;

    public ReorderProjectsCommandHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<bool> Handle(ReorderProjectsCommand request, CancellationToken cancellationToken)
    {
        var repository = _unitOfWork.Repository<Project>();
        var projects = await repository.GetAllAsync();
        var byId = projects.ToDictionary(p => p.Id);

        foreach (var item in request.Items)
        {
            if (!byId.TryGetValue(item.Id, out var project))
                throw new NotFoundException(nameof(Project), item.Id);

            project.Order = item.Order;
            repository.Update(project);
        }

        return await _unitOfWork.Complete() > 0;
    }
}
