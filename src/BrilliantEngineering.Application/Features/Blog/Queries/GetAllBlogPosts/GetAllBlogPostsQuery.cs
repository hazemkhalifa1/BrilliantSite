using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Models;
using MediatR;

namespace BrilliantEngineering.Application.Features.Blog.Queries.GetAllBlogPosts;

public record GetAllBlogPostsQuery(bool? PublishedOnly, int? TagId, int PageIndex, int PageSize)
    : IRequest<PagedResult<BlogPostDto>>;
