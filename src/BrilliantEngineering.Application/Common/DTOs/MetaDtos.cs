namespace BrilliantEngineering.Application.Common.DTOs;

public record SitemapItemDto(
    string Url,
    DateTime LastModified,
    string ChangeFrequency,
    double Priority);

public record MetaDto(
    string Title,
    string Description,
    string? Keywords,
    string CanonicalUrl,
    string? OgImage,
    string? OgType);
