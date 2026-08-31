using BrilliantEngineering.Application.Common.Exceptions;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Services.Commands.ReorderServices;

public class ReorderServicesCommandHandler : IRequestHandler<ReorderServicesCommand, bool>
{
    private readonly IUnitOfWork _unitOfWork;

    public ReorderServicesCommandHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<bool> Handle(ReorderServicesCommand request, CancellationToken cancellationToken)
    {
        var repository = _unitOfWork.Repository<Service>();
        var services = await repository.GetAllAsync();
        var byId = services.ToDictionary(s => s.Id);

        foreach (var item in request.Items)
        {
            if (!byId.TryGetValue(item.Id, out var service))
                throw new NotFoundException(nameof(Service), item.Id);

            service.Order = item.Order;
            repository.Update(service);
        }

        return await _unitOfWork.Complete() > 0;
    }
}
