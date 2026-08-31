using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.Social.Commands.ReorderSocialLinks;

public record ReorderSocialLinksCommand(IReadOnlyList<ReorderItemDto> Items) : IRequest<bool>;
