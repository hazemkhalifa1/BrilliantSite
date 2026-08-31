using BrilliantEngineering.API.Common;
using BrilliantEngineering.Application.Features.Team.Commands.CreateTeamMember;
using BrilliantEngineering.Application.Features.Team.Commands.DeleteTeamMember;
using BrilliantEngineering.Application.Features.Team.Commands.ReorderTeamMembers;
using BrilliantEngineering.Application.Features.Team.Commands.UpdateTeamMember;
using BrilliantEngineering.Application.Features.Team.Queries.GetAllTeamMembers;
using BrilliantEngineering.Application.Features.Team.Queries.GetTeamMemberById;
using MediatR;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;

namespace BrilliantEngineering.API.Controllers;

[Route("api/team")]
[Authorize(Roles = "Admin")]
public class TeamController : BaseController
{
    public TeamController(IMediator mediator) : base(mediator)
    {
    }

    [HttpGet]
    [AllowAnonymous]
    public async Task<IActionResult> GetAll(
        [FromQuery] bool? onlyActive,
        [FromQuery] int pageIndex = 1,
        [FromQuery] int pageSize = 10)
    {
        var result = await Mediator.Send(new GetAllTeamMembersQuery(onlyActive, pageIndex, pageSize));
        return OkResponse(result);
    }

    [HttpGet("{id:int}")]
    [AllowAnonymous]
    public async Task<IActionResult> GetById(int id)
    {
        var result = await Mediator.Send(new GetTeamMemberByIdQuery(id));
        return OkResponse(result);
    }

    [HttpPost]
    public async Task<IActionResult> Create([FromBody] CreateTeamMemberCommand command)
    {
        var result = await Mediator.Send(command);
        return CreatedResponse(result);
    }

    [HttpPut("{id:int}")]
    public async Task<IActionResult> Update(int id, [FromBody] UpdateTeamMemberCommand command)
    {
        if (id != command.Id)
            return BadRequestResponse("Route id and body id must match.");

        var result = await Mediator.Send(command);
        return OkResponse(result, "Team member updated successfully.");
    }

    [HttpDelete("{id:int}")]
    public async Task<IActionResult> Delete(int id)
    {
        var result = await Mediator.Send(new DeleteTeamMemberCommand(id));
        return OkResponse(result, "Team member deleted successfully.");
    }

    [HttpPut("reorder")]
    public async Task<IActionResult> Reorder([FromBody] ReorderTeamMembersCommand command)
    {
        var result = await Mediator.Send(command);
        return OkResponse(result, "Team members reordered successfully.");
    }
}
