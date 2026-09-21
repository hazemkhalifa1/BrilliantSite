using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Application.Common.Exceptions;
using BrilliantEngineering.Application.Common.Specifications;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Services.Commands.UpdateService;

public class UpdateServiceCommandHandler : IRequestHandler<UpdateServiceCommand, ServiceDto>
{
    private readonly IUnitOfWork _unitOfWork;

    public UpdateServiceCommandHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<ServiceDto> Handle(UpdateServiceCommand request, CancellationToken cancellationToken)
    {
        var service = await _unitOfWork.Repository<Service>().GetByIdAsync(request.Id);
        if (service is null)
            throw new NotFoundException(nameof(Service), request.Id);

        var categoryExists = await _unitOfWork.Repository<ServiceCategory>().GetByIdAsync(request.CategoryId) is not null;
        if (!categoryExists)
            throw new NotFoundException(nameof(ServiceCategory), request.CategoryId);

        service.Title = request.Title;
        service.TitleAr = request.TitleAr;
        service.Description = request.Description;
        service.DescriptionAr = request.DescriptionAr;
        service.IconPath = request.IconPath;
        service.CategoryId = request.CategoryId;
        service.Order = request.Order;
        service.IsActive = request.IsActive;
        service.RelatedBlogPostId = request.RelatedBlogPostId;

        _unitOfWork.Repository<Service>().Update(service);
        await _unitOfWork.Complete();

        var updated = await _unitOfWork.Repository<Service>()
            .GetEntityWithSpecAsync(new ServiceByIdWithCategorySpec(service.Id));

        return updated!.ToDto();
    }
}
