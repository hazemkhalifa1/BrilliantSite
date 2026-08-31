using BrilliantEngineering.Domain.Entities;
using Microsoft.AspNetCore.Identity.EntityFrameworkCore;
using Microsoft.EntityFrameworkCore;

namespace BrilliantEngineering.Infrastructure.Data;

public class AppDbContext : IdentityDbContext<AppUser>
{
    public AppDbContext(DbContextOptions<AppDbContext> options) : base(options)
    {
    }

    public DbSet<Product> Products => Set<Product>();
    public DbSet<ProductBrand> ProductBrands => Set<ProductBrand>();
    public DbSet<ProductCategory> ProductCategories => Set<ProductCategory>();
    public DbSet<ServiceCategory> ServiceCategories => Set<ServiceCategory>();
    public DbSet<Service> Services => Set<Service>();
    public DbSet<ProjectType> ProjectTypes => Set<ProjectType>();
    public DbSet<Project> Projects => Set<Project>();
    public DbSet<BlogPost> BlogPosts => Set<BlogPost>();
    public DbSet<Tag> Tags => Set<Tag>();
    public DbSet<BlogPostTag> BlogPostTags => Set<BlogPostTag>();
    public DbSet<TeamMember> TeamMembers => Set<TeamMember>();
    public DbSet<Client> Clients => Set<Client>();
    public DbSet<SocialLink> SocialLinks => Set<SocialLink>();
    public DbSet<ContactInfo> ContactInfos => Set<ContactInfo>();
    public DbSet<HeroSection> HeroSections => Set<HeroSection>();
    public DbSet<HeroStat> HeroStats => Set<HeroStat>();

    protected override void OnModelCreating(ModelBuilder builder)
    {
        base.OnModelCreating(builder);

        builder.Entity<ProductBrand>(entity =>
        {
            entity.Property(e => e.Name).IsRequired().HasMaxLength(150);
            entity.HasMany(e => e.Categories)
                .WithOne(e => e.Brand)
                .HasForeignKey(e => e.BrandId)
                .OnDelete(DeleteBehavior.Cascade);
        });

        builder.Entity<ProductCategory>(entity =>
        {
            entity.Property(e => e.Name).IsRequired().HasMaxLength(150);
            entity.HasMany(e => e.Products)
                .WithOne(e => e.Category)
                .HasForeignKey(e => e.CategoryId)
                .OnDelete(DeleteBehavior.Cascade);
        });

        builder.Entity<ServiceCategory>(entity =>
        {
            entity.Property(e => e.Name).IsRequired().HasMaxLength(150);
            entity.HasMany(e => e.Services)
                .WithOne(e => e.Category)
                .HasForeignKey(e => e.CategoryId)
                .OnDelete(DeleteBehavior.Cascade);
        });

        builder.Entity<ProjectType>(entity =>
        {
            entity.Property(e => e.Name).IsRequired().HasMaxLength(150);
            entity.HasMany(e => e.Projects)
                .WithOne(e => e.Type)
                .HasForeignKey(e => e.TypeId)
                .OnDelete(DeleteBehavior.Cascade);
        });

        builder.Entity<BlogPost>(entity =>
        {
            entity.Property(e => e.Slug).IsRequired().HasMaxLength(200);
            entity.HasIndex(e => e.Slug).IsUnique();
        });

        builder.Entity<Tag>(entity =>
        {
            entity.Property(e => e.Slug).IsRequired().HasMaxLength(150);
            entity.HasIndex(e => e.Slug).IsUnique();
        });

        builder.Entity<BlogPostTag>(entity =>
        {
            entity.HasKey(e => new { e.BlogPostId, e.TagId });
            entity.HasOne(e => e.BlogPost)
                .WithMany(e => e.BlogPostTags)
                .HasForeignKey(e => e.BlogPostId)
                .OnDelete(DeleteBehavior.Cascade);
            entity.HasOne(e => e.Tag)
                .WithMany(e => e.BlogPostTags)
                .HasForeignKey(e => e.TagId)
                .OnDelete(DeleteBehavior.Cascade);
        });
    }
}
