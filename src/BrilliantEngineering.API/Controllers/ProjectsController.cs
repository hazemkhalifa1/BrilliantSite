using BrilliantEngineering.API.Common;
using BrilliantEngineering.Application.Features.Projects.Commands.CreateProject;
using BrilliantEngineering.Application.Features.Projects.Commands.DeleteProject;
using BrilliantEngineering.Application.Features.Projects.Commands.ReorderProjects;
using BrilliantEngineering.Application.Features.Projects.Commands.UpdateProject;
using BrilliantEngineering.Application.Features.Projects.Queries.GetAllProjects;
using BrilliantEngineering.Application.Features.Projects.Queries.GetProjectById;
using MediatR;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;

namespace BrilliantEngineering.API.Controllers;

[Route("api/projects")]
[Authorize(Roles = "Admin")]
public class ProjectsController : BaseController
{
    public ProjectsController(IMediator mediator) : base(mediator)
    {
    }

    [HttpGet]
    [AllowAnonymous]
    public async Task<IActionResult> GetAll(
        [FromQuery] int? typeId,
        [FromQuery] bool? onlyActive,
        [FromQuery] int pageIndex = 1,
        [FromQuery] int pageSize = 10)
    {
        var result = await Mediator.Send(new GetAllProjectsQuery(typeId, onlyActive, pageIndex, pageSize));
        return OkResponse(result);
    }

    [HttpGet("{id:int}")]
    [AllowAnonymous]
    public async Task<IActionResult> GetById(int id)
    {
        var result = await Mediator.Send(new GetProjectByIdQuery(id));
        return OkResponse(result);
    }

    [HttpPost]
    public async Task<IActionResult> Create([FromBody] CreateProjectCommand command)
    {
        var result = await Mediator.Send(command);
        return CreatedResponse(result);
    }

    [HttpPut("{id:int}")]
    public async Task<IActionResult> Update(int id, [FromBody] UpdateProjectCommand command)
    {
        if (id != command.Id)
            return BadRequestResponse("Route id and body id must match.");

        var result = await Mediator.Send(command);
        return OkResponse(result, "Project updated successfully.");
    }

    [HttpDelete("{id:int}")]
    public async Task<IActionResult> Delete(int id)
    {
        var result = await Mediator.Send(new DeleteProjectCommand(id));
        return OkResponse(result, "Project deleted successfully.");
    }

    [HttpPut("reorder")]
    public async Task<IActionResult> Reorder([FromBody] ReorderProjectsCommand command)
    {
        var result = await Mediator.Send(command);
        return OkResponse(result, "Projects reordered successfully.");
    }
}
