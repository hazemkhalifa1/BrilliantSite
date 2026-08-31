using BrilliantEngineering.API.Common;
using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Features.Services.Commands.CreateService;
using BrilliantEngineering.Application.Features.Services.Commands.DeleteService;
using BrilliantEngineering.Application.Features.Services.Commands.ReorderServices;
using BrilliantEngineering.Application.Features.Services.Commands.UpdateService;
using BrilliantEngineering.Application.Features.Services.Queries.GetAllServices;
using BrilliantEngineering.Application.Features.Services.Queries.GetServiceById;
using MediatR;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;

namespace BrilliantEngineering.API.Controllers;

[Route("api/services")]
[Authorize(Roles = "Admin")]
public class ServicesController : BaseController
{
    public ServicesController(IMediator mediator) : base(mediator)
    {
    }

    [HttpGet]
    [AllowAnonymous]
    public async Task<IActionResult> GetAll(
        [FromQuery] int? categoryId,
        [FromQuery] bool? onlyActive,
        [FromQuery] string? search,
        [FromQuery] int pageIndex = 1,
        [FromQuery] int pageSize = 10)
    {
        var result = await Mediator.Send(new GetAllServicesQuery(categoryId, onlyActive, search, pageIndex, pageSize));
        return OkResponse(result);
    }

    [HttpGet("{id:int}")]
    [AllowAnonymous]
    public async Task<IActionResult> GetById(int id)
    {
        var result = await Mediator.Send(new GetServiceByIdQuery(id));
        return OkResponse(result);
    }

    [HttpPost]
    public async Task<IActionResult> Create([FromBody] CreateServiceCommand command)
    {
        var result = await Mediator.Send(command);
        return CreatedResponse(result);
    }

    [HttpPut("{id:int}")]
    public async Task<IActionResult> Update(int id, [FromBody] UpdateServiceCommand command)
    {
        if (id != command.Id)
            return BadRequestResponse("Route id and body id must match.");

        var result = await Mediator.Send(command);
        return OkResponse(result, "Service updated successfully.");
    }

    [HttpPut("Bulk")]
    public async Task<IActionResult> UpdateBulk([FromBody] List<UpdateServiceCommand> commands)
    {
        var results = new List<ServiceDto>();
        foreach (var command in commands)
        {
            var result = await Mediator.Send(command);
            results.Add(result);
        }
        return OkResponse(results, "Services updated successfully.");
    }

    [HttpDelete("{id:int}")]
    public async Task<IActionResult> Delete(int id)
    {
        var result = await Mediator.Send(new DeleteServiceCommand(id));
        return OkResponse(result, "Service deleted successfully.");
    }

    [HttpPut("reorder")]
    public async Task<IActionResult> Reorder([FromBody] ReorderServicesCommand command)
    {
        var result = await Mediator.Send(command);
        return OkResponse(result, "Services reordered successfully.");
    }
}
