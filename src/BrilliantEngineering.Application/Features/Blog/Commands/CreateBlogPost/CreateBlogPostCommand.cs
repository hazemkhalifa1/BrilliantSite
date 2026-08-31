using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.Blog.Commands.CreateBlogPost;

public record CreateBlogPostCommand(
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
