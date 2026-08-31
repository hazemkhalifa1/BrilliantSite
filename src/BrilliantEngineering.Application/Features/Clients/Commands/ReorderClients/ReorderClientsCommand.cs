using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.Clients.Commands.ReorderClients;

public record ReorderClientsCommand(IReadOnlyList<ReorderItemDto> Items) : IRequest<bool>;
