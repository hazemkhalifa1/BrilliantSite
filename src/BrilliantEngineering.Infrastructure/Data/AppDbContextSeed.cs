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

        if (!await context.Clients.AnyAsync())
        {
            context.Clients.AddRange(
                new Client { Name = "Nile Developments", NameAr = "نايل ديفلوبرت", LogoPath = "/clients/nile-developments.svg", Order = 1 },
                new Client { Name = "Delta Manufacturing", NameAr = "دلتا للتصنيع", LogoPath = "/clients/delta-manufacturing.svg", Order = 2 },
                new Client { Name = "Port Said Logistics", NameAr = "بورسعيد للوجستيات", LogoPath = "/clients/port-said-logistics.svg", Order = 3 },
                new Client { Name = "Cairo Medical Group", NameAr = "مجموعة القاهرة الطبية", LogoPath = "/clients/cairo-medical-group.svg", Order = 4 },
                new Client { Name = "Fayoum Agro", NameAr = "الفيوم للأغذية", LogoPath = "/clients/fayoum-agro.svg", Order = 5 },
                new Client { Name = "Red Sea Resorts", NameAr = "منتجعات البحر الأحمر", LogoPath = "/clients/red-sea-resorts.svg", Order = 6 },
                new Client { Name = "Upper Egypt Cement", NameAr = "أسمنت الصعيد", LogoPath = "/clients/upper-egypt-cement.svg", Order = 7 },
                new Client { Name = "Maritime Works Co.", NameAr = "شركة الأعمال البحرية", LogoPath = "/clients/maritime-works.svg", Order = 8 });
            logger.LogInformation("Seeded sample clients");
        }
        else
        {
            var pendingLogos = new (string Name, string LogoPath)[]
            {
                ("Nile Developments", "/clients/nile-developments.svg"),
                ("Delta Manufacturing", "/clients/delta-manufacturing.svg"),
                ("Port Said Logistics", "/clients/port-said-logistics.svg"),
                ("Cairo Medical Group", "/clients/cairo-medical-group.svg"),
                ("Fayoum Agro", "/clients/fayoum-agro.svg"),
                ("Red Sea Resorts", "/clients/red-sea-resorts.svg"),
                ("Upper Egypt Cement", "/clients/upper-egypt-cement.svg"),
                ("Maritime Works Co.", "/clients/maritime-works.svg"),
            };

            var updated = 0;
            foreach (var (name, logoPath) in pendingLogos)
            {
                var existing = await context.Clients
                    .FirstOrDefaultAsync(c => c.Name == name && string.IsNullOrEmpty(c.LogoPath));
                if (existing is not null)
                {
                    existing.LogoPath = logoPath;
                    updated++;
                }
            }

            if (updated > 0) logger.LogInformation("Attached client logos ({Count})", updated);
        }

        await context.SaveChangesAsync();
    }
}
