using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.Clients.Queries.GetClientById;

public record GetClientByIdQuery(int Id) : IRequest<ClientDto>;
