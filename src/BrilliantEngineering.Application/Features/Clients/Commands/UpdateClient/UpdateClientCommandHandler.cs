using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Application.Common.Exceptions;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Clients.Commands.UpdateClient;

public class UpdateClientCommandHandler : IRequestHandler<UpdateClientCommand, ClientDto>
{
    private readonly IUnitOfWork _unitOfWork;

    public UpdateClientCommandHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<ClientDto> Handle(UpdateClientCommand request, CancellationToken cancellationToken)
    {
        var client = await _unitOfWork.Repository<Client>().GetByIdAsync(request.Id);
        if (client is null)
            throw new NotFoundException(nameof(Client), request.Id);

        client.Name = request.Name;
        client.NameAr = request.NameAr;
        client.LogoPath = request.LogoPath;
        client.IsActive = request.IsActive;

        _unitOfWork.Repository<Client>().Update(client);
        await _unitOfWork.Complete();

        return client.ToDto();
    }
}
