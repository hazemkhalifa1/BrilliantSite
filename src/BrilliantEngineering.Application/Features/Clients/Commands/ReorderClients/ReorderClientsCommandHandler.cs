using BrilliantEngineering.Application.Common.Exceptions;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Clients.Commands.ReorderClients;

public class ReorderClientsCommandHandler : IRequestHandler<ReorderClientsCommand, bool>
{
    private readonly IUnitOfWork _unitOfWork;

    public ReorderClientsCommandHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<bool> Handle(ReorderClientsCommand request, CancellationToken cancellationToken)
    {
        var repository = _unitOfWork.Repository<Client>();
        var clients = await repository.GetAllAsync();
        var byId = clients.ToDictionary(c => c.Id);

        foreach (var item in request.Items)
        {
            if (!byId.TryGetValue(item.Id, out var client))
                throw new NotFoundException(nameof(Client), item.Id);

            client.Order = item.Order;
            repository.Update(client);
        }

        return await _unitOfWork.Complete() > 0;
    }
}
