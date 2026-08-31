using BrilliantEngineering.API.Common;
using BrilliantEngineering.Application.Features.Sitemap.Queries.GetSitemap;
using MediatR;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;

namespace BrilliantEngineering.API.Controllers;

[Route("api/sitemap")]
[Authorize(Roles = "Admin")]
public class SitemapController : BaseController
{
    public SitemapController(IMediator mediator) : base(mediator)
    {
    }

    [HttpGet]
    [AllowAnonymous]
    public async Task<IActionResult> Get()
    {
        var baseUrl = GetBaseUrl();
        var result = await Mediator.Send(new GetSitemapQuery(baseUrl));
        return OkResponse(result);
    }

    private string? GetBaseUrl()
    {
        if (Request.Scheme == null || Request.Host.HasValue == false)
            return null;

        return $"{Request.Scheme}://{Request.Host.Value}";
    }
}
