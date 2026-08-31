using BrilliantEngineering.Application.Common.Exceptions;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.ProductCategories.Commands.DeleteProductCategory;

public class DeleteProductCategoryCommandHandler : IRequestHandler<DeleteProductCategoryCommand, bool>
{
    private readonly IUnitOfWork _unitOfWork;

    public DeleteProductCategoryCommandHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<bool> Handle(DeleteProductCategoryCommand request, CancellationToken cancellationToken)
    {
        var category = await _unitOfWork.Repository<ProductCategory>().GetByIdAsync(request.Id);
        if (category is null)
            throw new NotFoundException(nameof(ProductCategory), request.Id);

        _unitOfWork.Repository<ProductCategory>().Delete(category);
        return await _unitOfWork.Complete() > 0;
    }
}
