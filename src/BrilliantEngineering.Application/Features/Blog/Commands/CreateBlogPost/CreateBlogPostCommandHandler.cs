using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Application.Common.Helpers;
using BrilliantEngineering.Application.Common.Specifications;
using BrilliantEngineering.Domain.Common;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Blog.Commands.CreateBlogPost;

public class CreateBlogPostCommandHandler : IRequestHandler<CreateBlogPostCommand, BlogPostDto>
{
    private readonly IUnitOfWork _unitOfWork;

    public CreateBlogPostCommandHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<BlogPostDto> Handle(CreateBlogPostCommand request, CancellationToken cancellationToken)
    {
        var repository = _unitOfWork.Repository<BlogPost>();

        async Task<bool> SlugExists(string slug) => await repository.CountAsync(new BlogPostSlugExistsSpec(slug)) > 0;

        var slug = SlugHelper.MakeUnique(SlugHelper.Generate(request.Title), SlugExists);

        var blogPost = new BlogPost
        {
            Title = request.Title,
            TitleAr = request.TitleAr,
            Content = request.Content,
            ContentAr = request.ContentAr,
            CoverImagePath = request.CoverImagePath,
            Slug = slug,
            MetaTitle = string.IsNullOrWhiteSpace(request.MetaTitle) ? request.Title : request.MetaTitle,
            MetaTitleAr = request.MetaTitleAr,
            MetaDescription = request.MetaDescription,
            MetaDescriptionAr = request.MetaDescriptionAr,
            Order = request.Order,
            IsPublished = request.IsPublished,
            PublishedAt = request.IsPublished ? DateTime.UtcNow : null,
        };

        blogPost.BlogPostTags = request.TagIds
            .Select(tagId => new BlogPostTag { BlogPost = blogPost, TagId = tagId })
            .ToList();

        repository.Add(blogPost);
        await _unitOfWork.Complete();

        var created = await repository.GetEntityWithSpecAsync(new BlogPostByIdWithTagsSpec(blogPost.Id));

        return created!.ToDto();
    }
}

internal class BlogPostSlugExistsSpec : BaseSpecification<BlogPost>
{
    public BlogPostSlugExistsSpec(string slug)
    {
        ApplyCriteria(p => p.Slug == slug);
    }
}
