using MediatR;

namespace BrilliantEngineering.Application.Features.Services.Commands.DeleteService;

public record DeleteServiceCommand(int Id) : IRequest<bool>;
