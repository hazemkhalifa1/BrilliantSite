using System.Security.Claims;
using System.Text;
using BrilliantEngineering.Infrastructure.Data;
using BrilliantEngineering.Infrastructure.Settings;
using Microsoft.Extensions.Options;
using Microsoft.IdentityModel.JsonWebTokens;
using Microsoft.IdentityModel.Tokens;

namespace BrilliantEngineering.Infrastructure.Identity;

public interface ITokenService
{
    string CreateAccessToken(AppUser user, IReadOnlyList<string> roles);
    string CreateRefreshToken();
    ClaimsPrincipal? ValidateRefreshToken(string refreshToken);
}

public class TokenService : ITokenService
{
    private const string RefreshTokenTypeClaim = "token_type";
    private const string RefreshTokenTypeValue = "refresh";

    private readonly JwtSettings _settings;

    public TokenService(IOptions<JwtSettings> options)
    {
        _settings = options.Value;
    }

    public string CreateAccessToken(AppUser user, IReadOnlyList<string> roles)
    {
        var claims = new List<Claim>
        {
            new(JwtRegisteredClaimNames.Sub, user.Id),
            new(JwtRegisteredClaimNames.Email, user.Email ?? string.Empty),
            new(JwtRegisteredClaimNames.Jti, Guid.NewGuid().ToString()),
            new(JwtRegisteredClaimNames.Iat, new DateTimeOffset(DateTime.UtcNow).ToUnixTimeSeconds().ToString(), ClaimValueTypes.Integer64),
        };

        if (!string.IsNullOrWhiteSpace(user.FullName))
            claims.Add(new Claim("name", user.FullName));

        claims.AddRange(roles.Select(role => new Claim(ClaimTypes.Role, role)));

        var descriptor = new SecurityTokenDescriptor
        {
            Issuer = _settings.Issuer,
            Audience = _settings.Audience,
            Subject = new ClaimsIdentity(claims),
            Expires = DateTime.UtcNow.AddMinutes(_settings.ExpiryMinutes),
            SigningCredentials = new SigningCredentials(GetSigningKey(), SecurityAlgorithms.HmacSha256),
        };

        return new JsonWebTokenHandler().CreateToken(descriptor);
    }

    public string CreateRefreshToken()
    {
        var claims = new List<Claim>
        {
            new(JwtRegisteredClaimNames.Jti, Guid.NewGuid().ToString()),
            new(RefreshTokenTypeClaim, RefreshTokenTypeValue),
        };

        var descriptor = new SecurityTokenDescriptor
        {
            Issuer = _settings.Issuer,
            Audience = _settings.Audience,
            Subject = new ClaimsIdentity(claims),
            Expires = DateTime.UtcNow.AddDays(_settings.RefreshTokenExpiryDays),
            SigningCredentials = new SigningCredentials(GetSigningKey(), SecurityAlgorithms.HmacSha256),
        };

        return new JsonWebTokenHandler().CreateToken(descriptor);
    }

    public ClaimsPrincipal? ValidateRefreshToken(string refreshToken)
    {
        var validationParameters = new TokenValidationParameters
        {
            ValidateIssuerSigningKey = true,
            IssuerSigningKey = GetSigningKey(),
            ValidateIssuer = true,
            ValidIssuer = _settings.Issuer,
            ValidateAudience = true,
            ValidAudience = _settings.Audience,
            ValidateLifetime = true,
            ClockSkew = TimeSpan.FromMinutes(1),
        };

        try
        {
            var result = new JsonWebTokenHandler().ValidateTokenAsync(refreshToken, validationParameters).GetAwaiter().GetResult();
            if (!result.IsValid || result.ClaimsIdentity is null)
                return null;

            var hasRefreshType = result.ClaimsIdentity.Claims.Any(c => c.Type == RefreshTokenTypeClaim && c.Value == RefreshTokenTypeValue);
            if (!hasRefreshType)
                return null;

            return new ClaimsPrincipal(result.ClaimsIdentity);
        }
        catch
        {
            return null;
        }
    }

    private SymmetricSecurityKey GetSigningKey()
        => new(Encoding.UTF8.GetBytes(_settings.SecretKey));
}
