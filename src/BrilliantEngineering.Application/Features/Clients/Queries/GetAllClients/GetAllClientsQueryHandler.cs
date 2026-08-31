using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Application.Common.Models;
using BrilliantEngineering.Application.Common.Specifications;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Clients.Queries.GetAllClients;

public class GetAllClientsQueryHandler : IRequestHandler<GetAllClientsQuery, PagedResult<ClientDto>>
{
    private readonly IUnitOfWork _unitOfWork;

    public GetAllClientsQueryHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<PagedResult<ClientDto>> Handle(GetAllClientsQuery request, CancellationToken cancellationToken)
    {
        var repository = _unitOfWork.Repository<Client>();

        var spec = new PagedClientsOrderedSpec(request.OnlyActive, request.PageIndex, request.PageSize);
        var countSpec = new ClientsOrderedSpec(request.OnlyActive);

        var clients = await repository.GetAllWithSpecAsync(spec);
        var totalCount = await repository.CountAsync(countSpec);

        var items = clients.ToDtoList();

        return PagedResult<ClientDto>.Create(items, totalCount, request.PageIndex, request.PageSize);
    }
}
