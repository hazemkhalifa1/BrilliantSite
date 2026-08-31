using Microsoft.AspNetCore.Identity;

namespace BrilliantEngineering.Infrastructure.Data;

public class AppUser : IdentityUser
{
    public string? FullName { get; set; }
}
