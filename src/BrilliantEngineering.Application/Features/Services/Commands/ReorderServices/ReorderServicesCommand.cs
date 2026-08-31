using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.Services.Commands.ReorderServices;

public record ReorderServicesCommand(IReadOnlyList<ReorderItemDto> Items) : IRequest<bool>;
