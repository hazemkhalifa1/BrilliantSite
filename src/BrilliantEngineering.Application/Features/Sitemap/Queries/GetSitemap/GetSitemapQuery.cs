using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.Sitemap.Queries.GetSitemap;

public record GetSitemapQuery(string? BaseUrl = null) : IRequest<IReadOnlyList<SitemapItemDto>>;
