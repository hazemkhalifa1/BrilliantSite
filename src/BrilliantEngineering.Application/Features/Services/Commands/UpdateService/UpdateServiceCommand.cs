using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.Services.Commands.UpdateService;

public record UpdateServiceCommand(
    int Id,
    string Title,
    string? TitleAr,
    string Description,
    string? DescriptionAr,
    string IconPath,
    int CategoryId,
    int Order,
    bool IsActive) : IRequest<ServiceDto>;
