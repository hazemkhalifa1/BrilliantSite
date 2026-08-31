using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Models;
using MediatR;

namespace BrilliantEngineering.Application.Features.Clients.Queries.GetAllClients;

public record GetAllClientsQuery(bool? OnlyActive, int PageIndex, int PageSize)
    : IRequest<PagedResult<ClientDto>>;
