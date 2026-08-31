using BrilliantEngineering.Application.Common.Exceptions;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.ServiceCategories.Commands.ReorderServiceCategories;

public class ReorderServiceCategoriesCommandHandler : IRequestHandler<ReorderServiceCategoriesCommand, bool>
{
    private readonly IUnitOfWork _unitOfWork;

    public ReorderServiceCategoriesCommandHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<bool> Handle(ReorderServiceCategoriesCommand request, CancellationToken cancellationToken)
    {
        var repository = _unitOfWork.Repository<ServiceCategory>();
        var categories = await repository.GetAllAsync();
        var byId = categories.ToDictionary(c => c.Id);

        foreach (var item in request.Items)
        {
            if (!byId.TryGetValue(item.Id, out var category))
                throw new NotFoundException(nameof(ServiceCategory), item.Id);

            category.Order = item.Order;
            repository.Update(category);
        }

        return await _unitOfWork.Complete() > 0;
    }
}
