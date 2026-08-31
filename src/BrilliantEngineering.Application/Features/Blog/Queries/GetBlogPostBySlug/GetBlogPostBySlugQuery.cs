using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.Blog.Queries.GetBlogPostBySlug;

public record GetBlogPostBySlugQuery(string Slug, bool PublishedOnly = true) : IRequest<BlogPostDto>;
