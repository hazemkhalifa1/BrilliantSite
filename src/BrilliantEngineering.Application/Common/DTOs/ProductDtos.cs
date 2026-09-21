namespace BrilliantEngineering.Application.Common.DTOs;

public record ProductDto(
    int Id = 0,
    string Name = "",
    string? NameAr = null,
    string Description = "",
    string? DescriptionAr = null,
    string ImagePath = "",
    string DocumentationUrl = "",
    int CategoryId = 0,
    string CategoryName = "",
    int? BrandId = null,
    string BrandName = "",
    bool IsActive = false,
    DateTime CreatedAt = default,
    int? RelatedBlogPostId = null,
    string? RelatedBlogPostTitle = null,
    string? RelatedBlogPostTitleAr = null,
    string? RelatedBlogPostSlug = null);

public record ProductBrandDto(
    int Id,
    string Name,
    string? NameAr,
    string Description,
    string? DescriptionAr,
    string BackgroundImagePath,
    bool IsActive,
    DateTime CreatedAt,
    IReadOnlyList<ProductCategoryDto>? Categories = null);

public record ProductCategoryDto(
    int Id,
    string Name,
    string? NameAr,
    int BrandId,
    string BrandName,
    bool IsActive,
    DateTime CreatedAt);
