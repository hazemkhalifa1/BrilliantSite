using BrilliantEngineering.API.Common;
using BrilliantEngineering.Application.Features.ProjectTypes.Commands.CreateProjectType;
using BrilliantEngineering.Application.Features.ProjectTypes.Commands.DeleteProjectType;
using BrilliantEngineering.Application.Features.ProjectTypes.Commands.UpdateProjectType;
using BrilliantEngineering.Application.Features.ProjectTypes.Queries.GetAllProjectTypes;
using BrilliantEngineering.Application.Features.ProjectTypes.Queries.GetProjectTypeById;
using MediatR;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;

namespace BrilliantEngineering.API.Controllers;

[Route("api/project-types")]
[Authorize(Roles = "Admin")]
public class ProjectTypesController : BaseController
{
    public ProjectTypesController(IMediator mediator) : base(mediator)
    {
    }

    [HttpGet]
    [AllowAnonymous]
    public async Task<IActionResult> GetAll(
        [FromQuery] bool? onlyActive,
        [FromQuery] int pageIndex = 1,
        [FromQuery] int pageSize = 10)
    {
        var result = await Mediator.Send(new GetAllProjectTypesQuery(onlyActive, pageIndex, pageSize));
        return OkResponse(result);
    }

    [HttpGet("{id:int}")]
    [AllowAnonymous]
    public async Task<IActionResult> GetById(int id)
    {
        var result = await Mediator.Send(new GetProjectTypeByIdQuery(id));
        return OkResponse(result);
    }

    [HttpPost]
    public async Task<IActionResult> Create([FromBody] CreateProjectTypeCommand command)
    {
        var result = await Mediator.Send(command);
        return CreatedResponse(result);
    }

    [HttpPut("{id:int}")]
    public async Task<IActionResult> Update(int id, [FromBody] UpdateProjectTypeCommand command)
    {
        if (id != command.Id)
            return BadRequestResponse("Route id and body id must match.");

        var result = await Mediator.Send(command);
        return OkResponse(result, "Project type updated successfully.");
    }

    [HttpDelete("{id:int}")]
    public async Task<IActionResult> Delete(int id)
    {
        var result = await Mediator.Send(new DeleteProjectTypeCommand(id));
        return OkResponse(result, "Project type deleted successfully.");
    }
}
