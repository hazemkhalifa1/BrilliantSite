using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.ServiceCategories.Commands.CreateServiceCategory;

public record CreateServiceCategoryCommand(string Name, string? NameAr, string Description, string? DescriptionAr, int Order, bool IsActive) : IRequest<ServiceCategoryDto>;
