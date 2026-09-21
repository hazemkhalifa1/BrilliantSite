using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.Services.Commands.CreateService;

public record CreateServiceCommand(
    string Title,
    string? TitleAr,
    string Description,
    string? DescriptionAr,
    string IconPath,
    int CategoryId,
    int Order,
    bool IsActive,
    int? RelatedBlogPostId = null) : IRequest<ServiceDto>;
