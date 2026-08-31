namespace BrilliantEngineering.Application.Common.DTOs;

public record ContactDto(
    int Id,
    string Phone1,
    string Phone2,
    string Email,
    string Email2,
    string Address,
    string? AddressAr,
    string MapEmbedUrl,
    DateTime UpdatedAt);
