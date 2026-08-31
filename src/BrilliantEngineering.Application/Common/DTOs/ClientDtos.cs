namespace BrilliantEngineering.Application.Common.DTOs;

public record ClientDto(
    int Id,
    string Name,
    string? NameAr,
    string LogoPath,
    int Order,
    bool IsActive,
    DateTime CreatedAt);
