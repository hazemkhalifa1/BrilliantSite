using MediatR;

namespace BrilliantEngineering.Application.Features.Clients.Commands.DeleteClient;

public record DeleteClientCommand(int Id) : IRequest<bool>;
