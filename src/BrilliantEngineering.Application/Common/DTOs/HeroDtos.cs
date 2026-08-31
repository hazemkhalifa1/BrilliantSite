namespace BrilliantEngineering.Application.Common.DTOs;

public record HeroDto(
    int Id,
    string HeadlineTop,
    string? HeadlineTopAr,
    string HeadlineBottom,
    string? HeadlineBottomAr,
    string SubText,
    string? SubTextAr,
    string PrimaryBtnText,
    string? PrimaryBtnTextAr,
    string PrimaryBtnUrl,
    string SecondaryBtnText,
    string? SecondaryBtnTextAr,
    string SecondaryBtnUrl,
    DateTime UpdatedAt,
    IReadOnlyList<HeroStatDto> Stats);

public record HeroStatDto(
    int Id,
    string Value,
    string Label,
    string? LabelAr,
    int Order,
    bool IsActive,
    DateTime CreatedAt);
