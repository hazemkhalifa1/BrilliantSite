using BrilliantEngineering.API.Common;
using BrilliantEngineering.Application.Features.Clients.Commands.CreateClient;
using BrilliantEngineering.Application.Features.Clients.Commands.DeleteClient;
using BrilliantEngineering.Application.Features.Clients.Commands.ReorderClients;
using BrilliantEngineering.Application.Features.Clients.Commands.UpdateClient;
using BrilliantEngineering.Application.Features.Clients.Queries.GetAllClients;
using BrilliantEngineering.Application.Features.Clients.Queries.GetClientById;
using MediatR;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;

namespace BrilliantEngineering.API.Controllers;

[Route("api/clients")]
[Authorize(Roles = "Admin")]
public class ClientsController : BaseController
{
    public ClientsController(IMediator mediator) : base(mediator)
    {
    }

    [HttpGet]
    [AllowAnonymous]
    public async Task<IActionResult> GetAll(
        [FromQuery] bool? onlyActive,
        [FromQuery] int pageIndex = 1,
        [FromQuery] int pageSize = 10)
    {
        var result = await Mediator.Send(new GetAllClientsQuery(onlyActive, pageIndex, pageSize));
        return OkResponse(result);
    }

    [HttpGet("{id:int}")]
    [AllowAnonymous]
    public async Task<IActionResult> GetById(int id)
    {
        var result = await Mediator.Send(new GetClientByIdQuery(id));
        return OkResponse(result);
    }

    [HttpPost]
    public async Task<IActionResult> Create([FromBody] CreateClientCommand command)
    {
        var result = await Mediator.Send(command);
        return CreatedResponse(result);
    }

    [HttpPut("{id:int}")]
    public async Task<IActionResult> Update(int id, [FromBody] UpdateClientCommand command)
    {
        if (id != command.Id)
            return BadRequestResponse("Route id and body id must match.");

        var result = await Mediator.Send(command);
        return OkResponse(result, "Client updated successfully.");
    }

    [HttpDelete("{id:int}")]
    public async Task<IActionResult> Delete(int id)
    {
        var result = await Mediator.Send(new DeleteClientCommand(id));
        return OkResponse(result, "Client deleted successfully.");
    }

    [HttpPut("reorder")]
    public async Task<IActionResult> Reorder([FromBody] ReorderClientsCommand command)
    {
        var result = await Mediator.Send(command);
        return OkResponse(result, "Clients reordered successfully.");
    }
}
