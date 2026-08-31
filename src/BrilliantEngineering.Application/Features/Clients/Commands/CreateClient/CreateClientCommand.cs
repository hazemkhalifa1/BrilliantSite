using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.Clients.Commands.CreateClient;

public record CreateClientCommand(
    string Name,
    string? NameAr,
    string LogoPath,
    bool IsActive) : IRequest<ClientDto>;
