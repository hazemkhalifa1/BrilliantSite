using BrilliantEngineering.Application.Common.Exceptions;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.ServiceCategories.Commands.DeleteServiceCategory;

public class DeleteServiceCategoryCommandHandler : IRequestHandler<DeleteServiceCategoryCommand, bool>
{
    private readonly IUnitOfWork _unitOfWork;

    public DeleteServiceCategoryCommandHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<bool> Handle(DeleteServiceCategoryCommand request, CancellationToken cancellationToken)
    {
        var category = await _unitOfWork.Repository<ServiceCategory>().GetByIdAsync(request.Id);
        if (category is null)
            throw new NotFoundException(nameof(ServiceCategory), request.Id);

        _unitOfWork.Repository<ServiceCategory>().Delete(category);
        return await _unitOfWork.Complete() > 0;
    }
}
