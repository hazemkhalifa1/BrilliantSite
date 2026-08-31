using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.Blog.Commands.ReorderBlogPosts;

public record ReorderBlogPostsCommand(IReadOnlyList<ReorderItemDto> Items) : IRequest<bool>;
