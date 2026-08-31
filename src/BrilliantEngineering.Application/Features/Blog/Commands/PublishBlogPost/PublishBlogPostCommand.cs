using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.Blog.Commands.PublishBlogPost;

public record PublishBlogPostCommand(int Id) : IRequest<BlogPostDto>;
