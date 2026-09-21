using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Domain.Entities;

namespace BrilliantEngineering.Application.Common.Mappings;

public static class MappingExtensions
{
    public static ProductDto ToDto(this Product src) => new(
        src.Id,
        src.Name,
        src.NameAr,
        src.Description,
        src.DescriptionAr,
        src.ImagePath,
        src.DocumentationUrl,
        src.CategoryId,
        src.Category != null ? src.Category.Name : string.Empty,
        src.Category != null ? src.Category.BrandId : (int?)null,
        src.Category != null && src.Category.Brand != null ? src.Category.Brand.Name : string.Empty,
        src.IsActive,
        src.CreatedAt,
        src.RelatedBlogPostId,
        src.RelatedBlogPost != null ? src.RelatedBlogPost.Title : null,
        src.RelatedBlogPost != null ? src.RelatedBlogPost.TitleAr : null,
        src.RelatedBlogPost != null ? src.RelatedBlogPost.Slug : null);

    public static IReadOnlyList<ProductDto> ToDtoList(this IEnumerable<Product> src)
        => src.Select(x => x.ToDto()).ToList();

    public static ProductBrandDto ToDto(this ProductBrand src) => new(
        src.Id,
        src.Name,
        src.NameAr,
        src.Description,
        src.DescriptionAr,
        src.BackgroundImagePath,
        src.IsActive,
        src.CreatedAt,
        src.Categories.Select(x => x.ToDto()).ToList());

    public static IReadOnlyList<ProductBrandDto> ToDtoList(this IEnumerable<ProductBrand> src)
        => src.Select(x => x.ToDto()).ToList();

    public static ProductCategoryDto ToDto(this ProductCategory src) => new(
        src.Id,
        src.Name,
        src.NameAr,
        src.BrandId,
        src.Brand != null ? src.Brand.Name : string.Empty,
        src.IsActive,
        src.CreatedAt);

    public static IReadOnlyList<ProductCategoryDto> ToDtoList(this IEnumerable<ProductCategory> src)
        => src.Select(x => x.ToDto()).ToList();

    public static ServiceDto ToDto(this Service src) => new(
        src.Id,
        src.Title,
        src.TitleAr,
        src.Description,
        src.DescriptionAr,
        src.IconPath,
        src.CategoryId,
        src.Category != null ? src.Category.Name : string.Empty,
        src.Order,
        src.IsActive,
        src.CreatedAt,
        src.RelatedBlogPostId,
        src.RelatedBlogPost != null ? src.RelatedBlogPost.Title : null,
        src.RelatedBlogPost != null ? src.RelatedBlogPost.TitleAr : null,
        src.RelatedBlogPost != null ? src.RelatedBlogPost.Slug : null);

    public static IReadOnlyList<ServiceDto> ToDtoList(this IEnumerable<Service> src)
        => src.Select(x => x.ToDto()).ToList();

    public static ServiceCategoryDto ToDto(this ServiceCategory src) => new(
        src.Id,
        src.Name,
        src.NameAr,
        src.Description,
        src.DescriptionAr,
        src.Order,
        src.IsActive,
        src.CreatedAt);

    public static IReadOnlyList<ServiceCategoryDto> ToDtoList(this IEnumerable<ServiceCategory> src)
        => src.Select(x => x.ToDto()).ToList();

    public static ProjectDto ToDto(this Project src) => new(
        src.Id,
        src.Title,
        src.TitleAr,
        src.Description,
        src.DescriptionAr,
        src.ImagePath,
        src.ClientName,
        src.ClientNameAr,
        src.Year,
        src.TypeId,
        src.Type != null ? src.Type.Name : string.Empty,
        src.Order,
        src.IsActive,
        src.CreatedAt);

    public static IReadOnlyList<ProjectDto> ToDtoList(this IEnumerable<Project> src)
        => src.Select(x => x.ToDto()).ToList();

    public static ProjectTypeDto ToDto(this ProjectType src) => new(
        src.Id,
        src.Name,
        src.NameAr,
        src.IsActive,
        src.CreatedAt);

    public static IReadOnlyList<ProjectTypeDto> ToDtoList(this IEnumerable<ProjectType> src)
        => src.Select(x => x.ToDto()).ToList();

    public static BlogPostDto ToDto(this BlogPost src) => new(
        src.Id,
        src.Title,
        src.TitleAr,
        src.Content,
        src.ContentAr,
        src.CoverImagePath,
        src.Slug,
        src.MetaTitle,
        src.MetaTitleAr,
        src.MetaDescription,
        src.MetaDescriptionAr,
        src.PublishedAt,
        src.IsPublished,
        src.Order,
        src.CreatedAt,
        src.BlogPostTags != null
            ? src.BlogPostTags.Select(x => x.Tag).Where(t => t != null).Select(t => t!.ToDto()).ToList()
            : null);

    public static IReadOnlyList<BlogPostDto> ToDtoList(this IEnumerable<BlogPost> src)
        => src.Select(x => x.ToDto()).ToList();

    public static TagDto ToDto(this Tag src) => new(
        src.Id,
        src.Name,
        src.Slug);

    public static IReadOnlyList<TagDto> ToDtoList(this IEnumerable<Tag> src)
        => src.Select(x => x.ToDto()).ToList();

    public static TeamMemberDto ToDto(this TeamMember src) => new(
        src.Id,
        src.Name,
        src.NameAr,
        src.JobTitle,
        src.JobTitleAr,
        src.Description,
        src.DescriptionAr,
        src.ImagePath,
        src.Order,
        src.IsActive,
        src.CreatedAt);

    public static IReadOnlyList<TeamMemberDto> ToDtoList(this IEnumerable<TeamMember> src)
        => src.Select(x => x.ToDto()).ToList();

    public static ClientDto ToDto(this Client src) => new(
        src.Id,
        src.Name,
        src.NameAr,
        src.LogoPath,
        src.Order,
        src.IsActive,
        src.CreatedAt);

    public static IReadOnlyList<ClientDto> ToDtoList(this IEnumerable<Client> src)
        => src.Select(x => x.ToDto()).ToList();

    public static TestimonialDto ToDto(this Testimonial src) => new(
        src.Id,
        src.Name,
        src.NameAr,
        src.Quote,
        src.QuoteAr,
        src.Role,
        src.RoleAr,
        src.ImagePath,
        src.Order,
        src.IsActive,
        src.CreatedAt);

    public static IReadOnlyList<TestimonialDto> ToDtoList(this IEnumerable<Testimonial> src)
        => src.Select(x => x.ToDto()).ToList();

    public static HeroDto ToDto(this HeroSection src) => new(
        src.Id,
        src.HeadlineTop,
        src.HeadlineTopAr,
        src.HeadlineBottom,
        src.HeadlineBottomAr,
        src.SubText,
        src.SubTextAr,
        src.PrimaryBtnText,
        src.PrimaryBtnTextAr,
        src.PrimaryBtnUrl,
        src.SecondaryBtnText,
        src.SecondaryBtnTextAr,
        src.SecondaryBtnUrl,
        src.UpdatedAt,
        Array.Empty<HeroStatDto>());

    public static HeroStatDto ToDto(this HeroStat src) => new(
        src.Id,
        src.Value,
        src.Label,
        src.LabelAr,
        src.Order,
        src.IsActive,
        src.CreatedAt);

    public static IReadOnlyList<HeroStatDto> ToDtoList(this IEnumerable<HeroStat> src)
        => src.Select(x => x.ToDto()).ToList();

    public static ContactDto ToDto(this ContactInfo src) => new(
        src.Id,
        src.Phone1,
        src.Phone2,
        src.Email,
        src.Email2,
        src.Address,
        src.AddressAr,
        src.MapEmbedUrl,
        src.UpdatedAt);

    public static SocialLinkDto ToDto(this SocialLink src) => new(
        src.Id,
        src.Platform,
        src.Url,
        src.IconClass,
        src.Order,
        src.IsActive,
        src.CreatedAt);

    public static IReadOnlyList<SocialLinkDto> ToDtoList(this IEnumerable<SocialLink> src)
        => src.Select(x => x.ToDto()).ToList();
}
