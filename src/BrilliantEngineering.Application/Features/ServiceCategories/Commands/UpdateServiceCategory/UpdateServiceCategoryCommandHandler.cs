using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Application.Common.Exceptions;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.ServiceCategories.Commands.UpdateServiceCategory;

public class UpdateServiceCategoryCommandHandler : IRequestHandler<UpdateServiceCategoryCommand, ServiceCategoryDto>
{
    private readonly IUnitOfWork _unitOfWork;

    public UpdateServiceCategoryCommandHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<ServiceCategoryDto> Handle(UpdateServiceCategoryCommand request, CancellationToken cancellationToken)
    {
        var category = await _unitOfWork.Repository<ServiceCategory>().GetByIdAsync(request.Id);
        if (category is null)
            throw new NotFoundException(nameof(ServiceCategory), request.Id);

        category.Name = request.Name;
        category.NameAr = request.NameAr;
        category.Description = request.Description;
        category.DescriptionAr = request.DescriptionAr;
        category.Order = request.Order;
        category.IsActive = request.IsActive;

        _unitOfWork.Repository<ServiceCategory>().Update(category);
        await _unitOfWork.Complete();

        return category.ToDto();
    }
}
