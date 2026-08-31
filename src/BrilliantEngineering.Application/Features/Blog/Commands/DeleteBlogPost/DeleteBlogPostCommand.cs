using MediatR;

namespace BrilliantEngineering.Application.Features.Blog.Commands.DeleteBlogPost;

public record DeleteBlogPostCommand(int Id) : IRequest<bool>;
