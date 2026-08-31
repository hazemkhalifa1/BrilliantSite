using BrilliantEngineering.Application.Common.Exceptions;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.ProjectTypes.Commands.DeleteProjectType;

public class DeleteProjectTypeCommandHandler : IRequestHandler<DeleteProjectTypeCommand, bool>
{
    private readonly IUnitOfWork _unitOfWork;

    public DeleteProjectTypeCommandHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<bool> Handle(DeleteProjectTypeCommand request, CancellationToken cancellationToken)
    {
        var type = await _unitOfWork.Repository<ProjectType>().GetByIdAsync(request.Id);
        if (type is null)
            throw new NotFoundException(nameof(ProjectType), request.Id);

        _unitOfWork.Repository<ProjectType>().Delete(type);
        return await _unitOfWork.Complete() > 0;
    }
}
