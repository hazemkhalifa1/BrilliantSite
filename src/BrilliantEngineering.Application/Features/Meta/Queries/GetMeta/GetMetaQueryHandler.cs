using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Specifications;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Meta.Queries.GetMeta;

public class GetMetaQueryHandler : IRequestHandler<GetMetaQuery, MetaDto>
{
    private readonly IUnitOfWork _unitOfWork;

    public GetMetaQueryHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<MetaDto> Handle(GetMetaQuery request, CancellationToken cancellationToken)
    {
        var baseUrl = (request.BaseUrl ?? string.Empty).TrimEnd('/');
        var page = (request.Page ?? string.Empty).Trim().TrimStart('/').ToLowerInvariant();
        var canonical = $"{baseUrl}/{page}";

        if (page.StartsWith("blog/", StringComparison.Ordinal))
        {
            var slug = page["blog/".Length..].TrimEnd('/');
            var post = await _unitOfWork.Repository<BlogPost>()
                .GetEntityWithSpecAsync(new BlogPostBySlugWithTagsSpec(slug, publishedOnly: true));

            if (post is not null)
            {
                return new MetaDto(
                    Title: post.MetaTitle,
                    Description: post.MetaDescription,
                    Keywords: string.Join(", ", post.BlogPostTags.Select(t => t.Tag?.Name ?? string.Empty).Where(n => n.Length > 0)),
                    CanonicalUrl: $"{baseUrl}/blog/{post.Slug}",
                    OgImage: string.IsNullOrWhiteSpace(post.CoverImagePath) ? null : post.CoverImagePath,
                    OgType: "article");
            }
        }

        return page switch
        {
            "" => new MetaDto(
                "Brilliant Engineering Co. — General Contracting in Egypt",
                "Brilliant Engineering Co. is a leading general contracting company in Egypt delivering civil construction, MEP works and turnkey projects.",
                "general contracting, construction, Egypt, civil works, MEP",
                $"{baseUrl}/",
                null,
                "website"),
            "services" => new MetaDto(
                "Our Services | Brilliant Engineering Co.",
                "Explore Brilliant Engineering's full range of services including civil construction and MEP works.",
                "services, civil construction, MEP works, Egypt",
                canonical,
                null,
                "website"),
            "products" => new MetaDto(
                "Our Products | Brilliant Engineering Co.",
                "Discover the brands and products supplied by Brilliant Engineering Co.",
                "products, brands, building materials, Egypt",
                canonical,
                null,
                "website"),
            "projects" => new MetaDto(
                "Our Projects | Brilliant Engineering Co.",
                "A portfolio of commercial and industrial projects delivered by Brilliant Engineering Co.",
                "projects, portfolio, commercial, industrial, Egypt",
                canonical,
                null,
                "website"),
            "blog" => new MetaDto(
                "Blog | Brilliant Engineering Co.",
                "News, insights and updates from Brilliant Engineering Co.",
                "blog, news, construction insights, Egypt",
                canonical,
                null,
                "website"),
            "team" => new MetaDto(
                "Our Team | Brilliant Engineering Co.",
                "Meet the engineers and professionals behind Brilliant Engineering Co.",
                "team, engineers, construction professionals, Egypt",
                canonical,
                null,
                "website"),
            "clients" => new MetaDto(
                "Our Clients | Brilliant Engineering Co.",
                "The partners and clients who trust Brilliant Engineering Co.",
                "clients, partners, construction, Egypt",
                canonical,
                null,
                "website"),
            "contact" => new MetaDto(
                "Contact Us | Brilliant Engineering Co.",
                "Get in touch with Brilliant Engineering Co. for your next construction project in Egypt.",
                "contact, construction company, Egypt",
                canonical,
                null,
                "website"),
            _ => new MetaDto(
                "Brilliant Engineering Co. — General Contracting in Egypt",
                "Brilliant Engineering Co. is a leading general contracting company in Egypt delivering civil construction, MEP works and turnkey projects.",
                "general contracting, construction, Egypt, civil works, MEP",
                canonical,
                null,
                "website"),
        };
    }
}
