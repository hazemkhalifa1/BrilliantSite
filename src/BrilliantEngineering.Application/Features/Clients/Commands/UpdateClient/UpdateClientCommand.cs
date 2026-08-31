using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.Clients.Commands.UpdateClient;

public record UpdateClientCommand(
    int Id,
    string Name,
    string? NameAr,
    string LogoPath,
    bool IsActive) : IRequest<ClientDto>;
