using BrilliantEngineering.API.Common;
using BrilliantEngineering.Application.Features.Hero.Commands.CreateHeroStat;
using BrilliantEngineering.Application.Features.Hero.Commands.DeleteHeroStat;
using BrilliantEngineering.Application.Features.Hero.Commands.ReorderHeroStats;
using BrilliantEngineering.Application.Features.Hero.Commands.UpdateHero;
using BrilliantEngineering.Application.Features.Hero.Commands.UpdateHeroStat;
using BrilliantEngineering.Application.Features.Hero.Queries.GetAllHeroStats;
using BrilliantEngineering.Application.Features.Hero.Queries.GetHero;
using BrilliantEngineering.Application.Features.Hero.Queries.GetHeroStatById;
using MediatR;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;

namespace BrilliantEngineering.API.Controllers;

[Route("api/hero")]
[Authorize(Roles = "Admin")]
public class HeroController : BaseController
{
    public HeroController(IMediator mediator) : base(mediator)
    {
    }

    [HttpGet]
    [AllowAnonymous]
    public async Task<IActionResult> Get()
    {
        var result = await Mediator.Send(new GetHeroQuery());
        return OkResponse(result);
    }

    [HttpPut]
    public async Task<IActionResult> Update([FromBody] UpdateHeroCommand command)
    {
        var result = await Mediator.Send(command);
        return OkResponse(result, "Hero section updated successfully.");
    }

    [HttpGet("stats")]
    [AllowAnonymous]
    public async Task<IActionResult> GetAllStats(
        [FromQuery] bool? onlyActive,
        [FromQuery] int pageIndex = 1,
        [FromQuery] int pageSize = 10)
    {
        var result = await Mediator.Send(new GetAllHeroStatsQuery(onlyActive, pageIndex, pageSize));
        return OkResponse(result);
    }

    [HttpGet("stats/{id:int}")]
    [AllowAnonymous]
    public async Task<IActionResult> GetStatById(int id)
    {
        var result = await Mediator.Send(new GetHeroStatByIdQuery(id));
        return OkResponse(result);
    }

    [HttpPost("stats")]
    public async Task<IActionResult> CreateStat([FromBody] CreateHeroStatCommand command)
    {
        var result = await Mediator.Send(command);
        return CreatedResponse(result);
    }

    [HttpPut("stats/{id:int}")]
    public async Task<IActionResult> UpdateStat(int id, [FromBody] UpdateHeroStatCommand command)
    {
        if (id != command.Id)
            return BadRequestResponse("Route id and body id must match.");

        var result = await Mediator.Send(command);
        return OkResponse(result, "Hero stat updated successfully.");
    }

    [HttpDelete("stats/{id:int}")]
    public async Task<IActionResult> DeleteStat(int id)
    {
        var result = await Mediator.Send(new DeleteHeroStatCommand(id));
        return OkResponse(result, "Hero stat deleted successfully.");
    }

    [HttpPut("stats/reorder")]
    public async Task<IActionResult> ReorderStats([FromBody] ReorderHeroStatsCommand command)
    {
        var result = await Mediator.Send(command);
        return OkResponse(result, "Hero stats reordered successfully.");
    }
}
