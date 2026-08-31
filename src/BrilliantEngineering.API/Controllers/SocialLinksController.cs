using BrilliantEngineering.API.Common;
using BrilliantEngineering.Application.Features.Social.Commands.CreateSocialLink;
using BrilliantEngineering.Application.Features.Social.Commands.DeleteSocialLink;
using BrilliantEngineering.Application.Features.Social.Commands.ReorderSocialLinks;
using BrilliantEngineering.Application.Features.Social.Commands.UpdateSocialLink;
using BrilliantEngineering.Application.Features.Social.Queries.GetAllSocialLinks;
using BrilliantEngineering.Application.Features.Social.Queries.GetSocialLinkById;
using MediatR;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;

namespace BrilliantEngineering.API.Controllers;

[Route("api/social-links")]
[Authorize(Roles = "Admin")]
public class SocialLinksController : BaseController
{
    public SocialLinksController(IMediator mediator) : base(mediator)
    {
    }

    [HttpGet]
    [AllowAnonymous]
    public async Task<IActionResult> GetAll(
        [FromQuery] bool? onlyActive,
        [FromQuery] int pageIndex = 1,
        [FromQuery] int pageSize = 10)
    {
        var result = await Mediator.Send(new GetAllSocialLinksQuery(onlyActive, pageIndex, pageSize));
        return OkResponse(result);
    }

    [HttpGet("{id:int}")]
    [AllowAnonymous]
    public async Task<IActionResult> GetById(int id)
    {
        var result = await Mediator.Send(new GetSocialLinkByIdQuery(id));
        return OkResponse(result);
    }

    [HttpPost]
    public async Task<IActionResult> Create([FromBody] CreateSocialLinkCommand command)
    {
        var result = await Mediator.Send(command);
        return CreatedResponse(result);
    }

    [HttpPut("{id:int}")]
    public async Task<IActionResult> Update(int id, [FromBody] UpdateSocialLinkCommand command)
    {
        if (id != command.Id)
            return BadRequestResponse("Route id and body id must match.");

        var result = await Mediator.Send(command);
        return OkResponse(result, "Social link updated successfully.");
    }

    [HttpDelete("{id:int}")]
    public async Task<IActionResult> Delete(int id)
    {
        var result = await Mediator.Send(new DeleteSocialLinkCommand(id));
        return OkResponse(result, "Social link deleted successfully.");
    }

    [HttpPut("reorder")]
    public async Task<IActionResult> Reorder([FromBody] ReorderSocialLinksCommand command)
    {
        var result = await Mediator.Send(command);
        return OkResponse(result, "Social links reordered successfully.");
    }
}
