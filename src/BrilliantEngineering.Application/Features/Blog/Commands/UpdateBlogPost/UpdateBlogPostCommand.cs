using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.Blog.Commands.UpdateBlogPost;

public record UpdateBlogPostCommand(
    int Id,
    string Title,
    string? TitleAr,
    string Content,
    string? ContentAr,
    string CoverImagePath,
    string MetaTitle,
    string? MetaTitleAr,
    string MetaDescription,
    string? MetaDescriptionAr,
    int Order,
    IReadOnlyList<int> TagIds,
    bool IsPublished) : IRequest<BlogPostDto>;
