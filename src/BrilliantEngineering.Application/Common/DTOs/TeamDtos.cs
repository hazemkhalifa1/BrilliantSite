namespace BrilliantEngineering.Application.Common.DTOs;

public record TeamMemberDto(
    int Id,
    string Name,
    string? NameAr,
    string JobTitle,
    string? JobTitleAr,
    string ImagePath,
    int Order,
    bool IsActive,
    DateTime CreatedAt);

public record ReorderItemDto(int Id, int Order);
