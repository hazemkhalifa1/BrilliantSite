using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Application.Common.Exceptions;
using BrilliantEngineering.Application.Common.Specifications;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Blog.Queries.GetBlogPostById;

public class GetBlogPostByIdQueryHandler : IRequestHandler<GetBlogPostByIdQuery, BlogPostDto>
{
    private readonly IUnitOfWork _unitOfWork;

    public GetBlogPostByIdQueryHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<BlogPostDto> Handle(GetBlogPostByIdQuery request, CancellationToken cancellationToken)
    {
        var post = await _unitOfWork.Repository<BlogPost>()
            .GetEntityWithSpecAsync(new BlogPostByIdWithTagsSpec(request.Id));

        if (post is null)
            throw new NotFoundException(nameof(BlogPost), request.Id);

        return post.ToDto();
    }
}
