using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.Blog.Commands.UnpublishBlogPost;

public record UnpublishBlogPostCommand(int Id) : IRequest<BlogPostDto>;
