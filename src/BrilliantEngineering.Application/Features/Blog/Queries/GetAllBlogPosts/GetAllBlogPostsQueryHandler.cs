using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Application.Common.Models;
using BrilliantEngineering.Application.Common.Specifications;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Blog.Queries.GetAllBlogPosts;

public class GetAllBlogPostsQueryHandler : IRequestHandler<GetAllBlogPostsQuery, PagedResult<BlogPostDto>>
{
    private readonly IUnitOfWork _unitOfWork;

    public GetAllBlogPostsQueryHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<PagedResult<BlogPostDto>> Handle(GetAllBlogPostsQuery request, CancellationToken cancellationToken)
    {
        var repository = _unitOfWork.Repository<BlogPost>();

        var spec = new PagedBlogPostsWithTagsSpec(request.PublishedOnly, request.TagId, request.PageIndex, request.PageSize);
        var countSpec = new BlogPostsWithTagsSpec(request.PublishedOnly, request.TagId);

        var posts = await repository.GetAllWithSpecAsync(spec);
        var totalCount = await repository.CountAsync(countSpec);

        var items = posts.ToDtoList();

        return PagedResult<BlogPostDto>.Create(items, totalCount, request.PageIndex, request.PageSize);
    }
}
