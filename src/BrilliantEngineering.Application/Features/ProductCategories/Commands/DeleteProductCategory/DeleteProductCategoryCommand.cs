using MediatR;

namespace BrilliantEngineering.Application.Features.ProductCategories.Commands.DeleteProductCategory;

public record DeleteProductCategoryCommand(int Id) : IRequest<bool>;
