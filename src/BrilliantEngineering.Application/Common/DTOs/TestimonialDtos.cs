namespace BrilliantEngineering.Application.Common.DTOs;

public record TestimonialDto(
    int Id,
    string Name,
    string? NameAr,
    string Quote,
    string? QuoteAr,
    string Role,
    string? RoleAr,
    string ImagePath,
    int Order,
    bool IsActive,
    DateTime CreatedAt);