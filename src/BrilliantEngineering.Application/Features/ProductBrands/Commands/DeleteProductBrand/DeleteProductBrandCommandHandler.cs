using BrilliantEngineering.Application.Common.Exceptions;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.ProductBrands.Commands.DeleteProductBrand;

public class DeleteProductBrandCommandHandler : IRequestHandler<DeleteProductBrandCommand, bool>
{
    private readonly IUnitOfWork _unitOfWork;

    public DeleteProductBrandCommandHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<bool> Handle(DeleteProductBrandCommand request, CancellationToken cancellationToken)
    {
        var brand = await _unitOfWork.Repository<ProductBrand>().GetByIdAsync(request.Id);
        if (brand is null)
            throw new NotFoundException(nameof(ProductBrand), request.Id);

        _unitOfWork.Repository<ProductBrand>().Delete(brand);
        return await _unitOfWork.Complete() > 0;
    }
}
