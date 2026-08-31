using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.ServiceCategories.Commands.UpdateServiceCategory;

public record UpdateServiceCategoryCommand(int Id, string Name, string? NameAr, string Description, string? DescriptionAr, int Order, bool IsActive) : IRequest<ServiceCategoryDto>;
