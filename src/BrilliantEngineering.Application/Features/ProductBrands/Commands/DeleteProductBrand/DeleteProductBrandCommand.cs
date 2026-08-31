using MediatR;

namespace BrilliantEngineering.Application.Features.ProductBrands.Commands.DeleteProductBrand;

public record DeleteProductBrandCommand(int Id) : IRequest<bool>;
