namespace BrilliantEngineering.Application.Common.DTOs;

public record TeamMemberDto(
    int Id,
    string Name,
    string? NameAr,
    string JobTitle,
    string? JobTitleAr,
    string Description,
    string? DescriptionAr,
    string ImagePath,
    int Order,
    bool IsActive,
    DateTime CreatedAt);

public record ReorderItemDto(int Id, int Order);
