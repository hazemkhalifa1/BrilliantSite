using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Application.Common.Exceptions;
using BrilliantEngineering.Application.Common.Specifications;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Services.Commands.CreateService;

public class CreateServiceCommandHandler : IRequestHandler<CreateServiceCommand, ServiceDto>
{
    private readonly IUnitOfWork _unitOfWork;

    public CreateServiceCommandHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<ServiceDto> Handle(CreateServiceCommand request, CancellationToken cancellationToken)
    {
        var categoryExists = await _unitOfWork.Repository<ServiceCategory>().GetByIdAsync(request.CategoryId) is not null;
        if (!categoryExists)
            throw new NotFoundException(nameof(ServiceCategory), request.CategoryId);

        var service = new Service
        {
            Title = request.Title,
            TitleAr = request.TitleAr,
            Description = request.Description,
            DescriptionAr = request.DescriptionAr,
            IconPath = request.IconPath,
            CategoryId = request.CategoryId,
            Order = request.Order,
            IsActive = request.IsActive,
        };

        _unitOfWork.Repository<Service>().Add(service);
        await _unitOfWork.Complete();

        var created = await _unitOfWork.Repository<Service>()
            .GetEntityWithSpecAsync(new ServiceByIdWithCategorySpec(service.Id));

        return created!.ToDto();
    }
}
