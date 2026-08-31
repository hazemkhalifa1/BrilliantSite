using BrilliantEngineering.API.Common;
using BrilliantEngineering.Application.Features.Blog.Commands.CreateTag;
using BrilliantEngineering.Application.Features.Blog.Commands.DeleteTag;
using BrilliantEngineering.Application.Features.Blog.Commands.UpdateTag;
using BrilliantEngineering.Application.Features.Blog.Queries.GetAllTags;
using BrilliantEngineering.Application.Features.Blog.Queries.GetTagById;
using MediatR;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Http.HttpResults;
using Microsoft.AspNetCore.Mvc;

namespace BrilliantEngineering.API.Controllers;

[Route("api/tags")]
[Authorize(Roles = "Admin")]
public class TagsController : BaseController
{
    public TagsController(IMediator mediator) : base(mediator)
    {
    }

    [HttpGet]
    [AllowAnonymous]
    public async Task<IActionResult> GetAll(
        [FromQuery] int pageIndex = 1,
        [FromQuery] int pageSize = 10)
    {
        var result = await Mediator.Send(new GetAllTagsQuery(pageIndex, pageSize));
        return OkResponse(result);
    }

    [HttpGet("{id:int}")]
    [AllowAnonymous]
    public async Task<IActionResult> GetById(int id)
    {
        var result = await Mediator.Send(new GetTagByIdQuery(id));
        return OkResponse(result);
    }

    [HttpPost]
    public async Task<IActionResult> Create([FromBody] CreateTagCommand command)
    {
        var result = await Mediator.Send(command);
        return CreatedResponse(result);
    }
 
    [HttpPost("Bulk")]
    public async Task<IActionResult> CreateBulk([FromBody] IEnumerable<CreateTagCommand> commands)
    {
        var results = new List<object>();
        foreach (var command in commands)
        {
            var result = await Mediator.Send(command);
            results.Add(result);
        }
        return CreatedResponse(results);
    }

    [HttpPut("{id:int}")]
    public async Task<IActionResult> Update(int id, [FromBody] UpdateTagCommand command)
    {
        if (id != command.Id)
            return BadRequestResponse("Route id and body id must match.");

        var result = await Mediator.Send(command);
        return OkResponse(result, "Tag updated successfully.");
    }

    [HttpDelete("{id:int}")]
    public async Task<IActionResult> Delete(int id)
    {
        var result = await Mediator.Send(new DeleteTagCommand(id));
        return OkResponse(result, "Tag deleted successfully.");
    }
}
