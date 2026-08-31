using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Application.Common.Exceptions;
using BrilliantEngineering.Application.Common.Specifications;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Blog.Queries.GetBlogPostBySlug;

public class GetBlogPostBySlugQueryHandler : IRequestHandler<GetBlogPostBySlugQuery, BlogPostDto>
{
    private readonly IUnitOfWork _unitOfWork;

    public GetBlogPostBySlugQueryHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<BlogPostDto> Handle(GetBlogPostBySlugQuery request, CancellationToken cancellationToken)
    {
        var post = await _unitOfWork.Repository<BlogPost>()
            .GetEntityWithSpecAsync(new BlogPostBySlugWithTagsSpec(request.Slug, request.PublishedOnly));

        if (post is null)
            throw new NotFoundException($"Blog post with slug '{request.Slug}' was not found.");

        return post.ToDto();
    }
}
