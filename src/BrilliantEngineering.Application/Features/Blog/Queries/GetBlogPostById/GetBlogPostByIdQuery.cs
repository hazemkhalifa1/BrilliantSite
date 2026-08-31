using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.Blog.Queries.GetBlogPostById;

public record GetBlogPostByIdQuery(int Id) : IRequest<BlogPostDto>;
