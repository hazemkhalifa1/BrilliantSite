using System.Security.Claims;
using BrilliantEngineering.API.Common;
using BrilliantEngineering.API.Common.Contracts;
using BrilliantEngineering.Infrastructure.Data;
using BrilliantEngineering.Infrastructure.Identity;
using BrilliantEngineering.Infrastructure.Settings;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Identity;
using Microsoft.AspNetCore.Mvc;
using Microsoft.Extensions.Options;
using Microsoft.IdentityModel.JsonWebTokens;

namespace BrilliantEngineering.API.Controllers;

[Route("api/auth")]
public class AuthController : ControllerBase
{
    private readonly UserManager<AppUser> _userManager;
    private readonly ITokenService _tokenService;
    private readonly JwtSettings _jwtSettings;

    public AuthController(
        UserManager<AppUser> userManager,
        ITokenService tokenService,
        IOptions<JwtSettings> jwtSettings)
    {
        _userManager = userManager;
        _tokenService = tokenService;
        _jwtSettings = jwtSettings.Value;
    }

    [HttpPost("login")]
    [AllowAnonymous]
    public async Task<ActionResult<ApiResponse<AuthResponse>>> Login([FromBody] LoginRequest request)
    {
        var user = await _userManager.FindByEmailAsync(request.Email);
        if (user is null || !await _userManager.CheckPasswordAsync(user, request.Password))
            return Unauthorized(ApiResponse<object>.Fail(StatusCodes.Status401Unauthorized, "Invalid email or password."));

        return Ok(ApiResponse<AuthResponse>.Ok(await BuildAuthResponseAsync(user), "Login successful."));
    }

    [HttpPost("refresh")]
    [AllowAnonymous]
    public async Task<ActionResult<ApiResponse<AuthResponse>>> Refresh([FromBody] RefreshRequest request)
    {
        var principal = _tokenService.ValidateRefreshToken(request.RefreshToken);
        if (principal is null)
            return Unauthorized(ApiResponse<object>.Fail(StatusCodes.Status401Unauthorized, "Invalid or expired refresh token."));

        var userId = principal.FindFirst(JwtRegisteredClaimNames.Sub)?.Value
            ?? principal.FindFirst(ClaimTypes.NameIdentifier)?.Value;

        if (userId is null)
            return Unauthorized(ApiResponse<object>.Fail(StatusCodes.Status401Unauthorized, "Invalid refresh token."));

        var user = await _userManager.FindByIdAsync(userId);
        if (user is null)
            return Unauthorized(ApiResponse<object>.Fail(StatusCodes.Status401Unauthorized, "Invalid refresh token."));

        return Ok(ApiResponse<AuthResponse>.Ok(await BuildAuthResponseAsync(user), "Token refreshed successfully."));
    }

    private async Task<AuthResponse> BuildAuthResponseAsync(AppUser user)
    {
        var roles = (await _userManager.GetRolesAsync(user)).ToList();
        var accessToken = _tokenService.CreateAccessToken(user, roles);
        var refreshToken = _tokenService.CreateRefreshToken();

        return new AuthResponse(
            accessToken,
            refreshToken,
            DateTime.UtcNow.AddMinutes(_jwtSettings.ExpiryMinutes),
            user.Email ?? string.Empty,
            user.FullName,
            roles);
    }
}
