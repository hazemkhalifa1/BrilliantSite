using BrilliantEngineering.Domain.Entities;
using Microsoft.AspNetCore.Identity;
using Microsoft.EntityFrameworkCore;
using Microsoft.Extensions.Configuration;
using Microsoft.Extensions.Logging;

namespace BrilliantEngineering.Infrastructure.Data;

public static class AppDbContextSeed
{
    public static async Task SeedAsync(
        AppDbContext context,
        UserManager<AppUser> userManager,
        RoleManager<IdentityRole> roleManager,
        IConfiguration configuration,
        ILogger logger)
    {
        const string adminRoleName = "Admin";

        if (!await roleManager.RoleExistsAsync(adminRoleName))
        {
            await roleManager.CreateAsync(new IdentityRole(adminRoleName));
            logger.LogInformation("Seeded '{Role}' role", adminRoleName);
        }

        var adminSection = configuration.GetSection("AdminSeed");
        var adminEmail = adminSection["Email"] ?? "admin@brilliant-eng.com";
        var adminPassword = adminSection["Password"] ?? "Admin@123456";

        if (await userManager.FindByEmailAsync(adminEmail) is null)
        {
            var adminUser = new AppUser
            {
                UserName = adminEmail,
                Email = adminEmail,
                FullName = "Brilliant Admin",
                EmailConfirmed = true,
            };

            var result = await userManager.CreateAsync(adminUser, adminPassword);
            if (result.Succeeded)
            {
                await userManager.AddToRoleAsync(adminUser, adminRoleName);
                logger.LogInformation("Seeded admin user '{Email}'", adminEmail);
            }
            else
            {
                logger.LogWarning("Failed to seed admin user: {Errors}", string.Join("; ", result.Errors.Select(e => e.Description)));
            }
        }

        if (!await context.ServiceCategories.AnyAsync())
        {
            context.ServiceCategories.AddRange(
                new ServiceCategory { Name = "Civil Construction" },
                new ServiceCategory { Name = "MEP Works" });
            logger.LogInformation("Seeded default service categories");
        }

        if (!await context.ProjectTypes.AnyAsync())
        {
            context.ProjectTypes.AddRange(
                new ProjectType { Name = "Commercial" },
                new ProjectType { Name = "Industrial" });
            logger.LogInformation("Seeded default project types");
        }

        if (!await context.HeroSections.AnyAsync())
        {
            context.HeroSections.Add(new HeroSection { UpdatedAt = DateTime.UtcNow });
            logger.LogInformation("Seeded default hero section");
        }

        if (!await context.ContactInfos.AnyAsync())
        {
            context.ContactInfos.Add(new ContactInfo { UpdatedAt = DateTime.UtcNow });
            logger.LogInformation("Seeded default contact info");
        }

        await context.SaveChangesAsync();
    }
}
