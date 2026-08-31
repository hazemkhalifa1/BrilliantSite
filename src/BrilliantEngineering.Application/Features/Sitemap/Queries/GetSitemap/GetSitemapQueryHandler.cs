using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Specifications;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Sitemap.Queries.GetSitemap;

public class GetSitemapQueryHandler : IRequestHandler<GetSitemapQuery, IReadOnlyList<SitemapItemDto>>
{
    private readonly IUnitOfWork _unitOfWork;

    public GetSitemapQueryHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<IReadOnlyList<SitemapItemDto>> Handle(GetSitemapQuery request, CancellationToken cancellationToken)
    {
        var baseUrl = (request.BaseUrl ?? string.Empty).TrimEnd('/');
        var items = new List<SitemapItemDto>();
        var now = DateTime.UtcNow;

        var staticPages = new[]
        {
            (Path: "", Frequency: "daily", Priority: 1.0),
            (Path: "about", Frequency: "monthly", Priority: 0.8),
            (Path: "services", Frequency: "weekly", Priority: 0.9),
            (Path: "products", Frequency: "weekly", Priority: 0.9),
            (Path: "projects", Frequency: "weekly", Priority: 0.9),
            (Path: "blog", Frequency: "daily", Priority: 0.8),
            (Path: "team", Frequency: "monthly", Priority: 0.6),
            (Path: "clients", Frequency: "monthly", Priority: 0.6),
            (Path: "contact", Frequency: "monthly", Priority: 0.7),
        };

        foreach (var page in staticPages)
        {
            items.Add(new SitemapItemDto(
                $"{baseUrl}/{page.Path}",
                now,
                page.Frequency,
                page.Priority));
        }

        var posts = await _unitOfWork.Repository<BlogPost>()
            .GetAllWithSpecAsync(new BlogPostsWithTagsSpec(publishedOnly: true, tagId: null));

        foreach (var post in posts)
        {
            items.Add(new SitemapItemDto(
                $"{baseUrl}/blog/{post.Slug}",
                post.PublishedAt ?? post.CreatedAt,
                "monthly",
                0.6));
        }

        return items;
    }
}
