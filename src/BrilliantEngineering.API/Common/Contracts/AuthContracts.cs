namespace BrilliantEngineering.API.Common.Contracts;

public record LoginRequest(string Email, string Password);

public record RefreshRequest(string RefreshToken);

public record AuthResponse(
    string Token,
    string RefreshToken,
    DateTime ExpiresAt,
    string Email,
    string? FullName,
    IReadOnlyList<string> Roles);
