using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Clients.Commands.CreateClient;

public class CreateClientCommandHandler : IRequestHandler<CreateClientCommand, ClientDto>
{
    private readonly IUnitOfWork _unitOfWork;

    public CreateClientCommandHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<ClientDto> Handle(CreateClientCommand request, CancellationToken cancellationToken)
    {
        var clients = await _unitOfWork.Repository<Client>().GetAllAsync();
        var nextOrder = clients.Count == 0 ? 0 : clients.Max(c => c.Order) + 1;

        var client = new Client
        {
            Name = request.Name,
            NameAr = request.NameAr,
            LogoPath = request.LogoPath,
            Order = nextOrder,
            IsActive = request.IsActive,
        };

        _unitOfWork.Repository<Client>().Add(client);
        await _unitOfWork.Complete();

        return client.ToDto();
    }
}
