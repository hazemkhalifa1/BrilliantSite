using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Application.Common.Exceptions;
using BrilliantEngineering.Application.Common.Specifications;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Blog.Commands.PublishBlogPost;

public class PublishBlogPostCommandHandler : IRequestHandler<PublishBlogPostCommand, BlogPostDto>
{
    private readonly IUnitOfWork _unitOfWork;

    public PublishBlogPostCommandHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<BlogPostDto> Handle(PublishBlogPostCommand request, CancellationToken cancellationToken)
    {
        var blogPost = await _unitOfWork.Repository<BlogPost>().GetByIdAsync(request.Id);
        if (blogPost is null)
            throw new NotFoundException(nameof(BlogPost), request.Id);

        blogPost.IsPublished = true;
        blogPost.PublishedAt ??= DateTime.UtcNow;

        _unitOfWork.Repository<BlogPost>().Update(blogPost);
        await _unitOfWork.Complete();

        var updated = await _unitOfWork.Repository<BlogPost>()
            .GetEntityWithSpecAsync(new BlogPostByIdWithTagsSpec(blogPost.Id));

        return updated!.ToDto();
    }
}
