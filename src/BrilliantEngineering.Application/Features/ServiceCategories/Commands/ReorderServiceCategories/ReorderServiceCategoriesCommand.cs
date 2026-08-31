using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.ServiceCategories.Commands.ReorderServiceCategories;

public record ReorderServiceCategoriesCommand(IReadOnlyList<ReorderItemDto> Items) : IRequest<bool>;
