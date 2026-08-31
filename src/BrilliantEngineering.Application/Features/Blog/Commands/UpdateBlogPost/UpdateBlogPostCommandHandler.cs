using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Application.Common.Exceptions;
using BrilliantEngineering.Application.Common.Helpers;
using BrilliantEngineering.Application.Common.Specifications;
using BrilliantEngineering.Domain.Common;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Blog.Commands.UpdateBlogPost;

public class UpdateBlogPostCommandHandler : IRequestHandler<UpdateBlogPostCommand, BlogPostDto>
{
    private readonly IUnitOfWork _unitOfWork;

    public UpdateBlogPostCommandHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<BlogPostDto> Handle(UpdateBlogPostCommand request, CancellationToken cancellationToken)
    {
        var repository = _unitOfWork.Repository<BlogPost>();

        var blogPost = await repository.GetEntityWithSpecAsync(new BlogPostByIdWithTagsSpec(request.Id));
        if (blogPost is null)
            throw new NotFoundException(nameof(BlogPost), request.Id);

        var slugChanged = !string.Equals(blogPost.Slug, SlugHelper.Generate(request.Title), StringComparison.OrdinalIgnoreCase);
        if (slugChanged)
        {
            async Task<bool> SlugExists(string slug) => await repository.CountAsync(new BlogPostSlugExistsSpec(slug, request.Id)) > 0;

            blogPost.Slug = SlugHelper.MakeUnique(SlugHelper.Generate(request.Title), SlugExists);
        }

        blogPost.Title = request.Title;
        blogPost.TitleAr = request.TitleAr;
        blogPost.Content = request.Content;
        blogPost.ContentAr = request.ContentAr;
        blogPost.CoverImagePath = request.CoverImagePath;
        blogPost.MetaTitle = string.IsNullOrWhiteSpace(request.MetaTitle) ? request.Title : request.MetaTitle;
        blogPost.MetaTitleAr = request.MetaTitleAr;
        blogPost.MetaDescription = request.MetaDescription;
        blogPost.MetaDescriptionAr = request.MetaDescriptionAr;
        blogPost.Order = request.Order;
        blogPost.IsPublished = request.IsPublished;
        blogPost.PublishedAt = request.IsPublished
            ? blogPost.PublishedAt ?? DateTime.UtcNow
            : null;

        blogPost.BlogPostTags.Clear();
        foreach (var tagId in request.TagIds)
            blogPost.BlogPostTags.Add(new BlogPostTag { BlogPostId = blogPost.Id, TagId = tagId });

        repository.Update(blogPost);
        await _unitOfWork.Complete();

        var updated = await repository.GetEntityWithSpecAsync(new BlogPostByIdWithTagsSpec(blogPost.Id));

        return updated!.ToDto();
    }
}

internal class BlogPostSlugExistsSpec : BaseSpecification<BlogPost>
{
    public BlogPostSlugExistsSpec(string slug, int excludeId)
    {
        ApplyCriteria(p => p.Slug == slug && p.Id != excludeId);
    }
}
