using MediatR;

namespace BrilliantEngineering.Application.Features.ServiceCategories.Commands.DeleteServiceCategory;

public record DeleteServiceCategoryCommand(int Id) : IRequest<bool>;
