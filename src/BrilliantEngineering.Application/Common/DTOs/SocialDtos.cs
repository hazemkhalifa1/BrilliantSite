namespace BrilliantEngineering.Application.Common.DTOs;

public record SocialLinkDto(
    int Id,
    string Platform,
    string Url,
    string IconClass,
    int Order,
    bool IsActive,
    DateTime CreatedAt);
