using BrilliantEngineering.API.Common;
using BrilliantEngineering.Application.Features.Meta.Queries.GetMeta;
using MediatR;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;

namespace BrilliantEngineering.API.Controllers;

[Route("api/meta")]
[Authorize(Roles = "Admin")]
public class MetaController : BaseController
{
    public MetaController(IMediator mediator) : base(mediator)
    {
    }

    [HttpGet("{page}")]
    [AllowAnonymous]
    public async Task<IActionResult> Get(string page)
    {
        var result = await Mediator.Send(new GetMetaQuery(page, GetBaseUrl()));
        return OkResponse(result);
    }

    private string? GetBaseUrl()
    {
        if (Request.Scheme == null || Request.Host.HasValue == false)
            return null;

        return $"{Request.Scheme}://{Request.Host.Value}";
    }
}
