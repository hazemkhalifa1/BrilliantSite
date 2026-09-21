namespace BrilliantEngineering.Application.Common.DTOs;

public record ServiceDto(
    int Id,
    string Title,
    string? TitleAr,
    string Description,
    string? DescriptionAr,
    string IconPath,
    int CategoryId,
    string CategoryName,
    int Order,
    bool IsActive,
    DateTime CreatedAt,
    int? RelatedBlogPostId = null,
    string? RelatedBlogPostTitle = null,
    string? RelatedBlogPostTitleAr = null,
    string? RelatedBlogPostSlug = null);

public record ServiceCategoryDto(
    int Id,
    string Name,
    string? NameAr,
    string Description,
    string? DescriptionAr,
    int Order,
    bool IsActive,
    DateTime CreatedAt);
