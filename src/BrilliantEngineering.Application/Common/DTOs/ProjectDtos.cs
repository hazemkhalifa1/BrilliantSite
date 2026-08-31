namespace BrilliantEngineering.Application.Common.DTOs;

public record ProjectDto(
    int Id,
    string Title,
    string? TitleAr,
    string Description,
    string? DescriptionAr,
    string ImagePath,
    string ClientName,
    string? ClientNameAr,
    int Year,
    int TypeId,
    string TypeName,
    int Order,
    bool IsActive,
    DateTime CreatedAt);

public record ProjectTypeDto(
    int Id,
    string Name,
    string? NameAr,
    bool IsActive,
    DateTime CreatedAt);
