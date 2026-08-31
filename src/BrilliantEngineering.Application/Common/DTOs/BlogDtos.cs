namespace BrilliantEngineering.Application.Common.DTOs;

public record BlogPostDto(
    int Id,
    string Title,
    string? TitleAr,
    string Content,
    string? ContentAr,
    string CoverImagePath,
    string Slug,
    string MetaTitle,
    string? MetaTitleAr,
    string MetaDescription,
    string? MetaDescriptionAr,
    DateTime? PublishedAt,
    bool IsPublished,
    int Order,
    DateTime CreatedAt,
    IReadOnlyList<TagDto>? Tags = null);

public record TagDto(
    int Id,
    string Name,
    string Slug);
