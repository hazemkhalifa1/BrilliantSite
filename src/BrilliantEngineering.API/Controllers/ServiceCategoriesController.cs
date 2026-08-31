using BrilliantEngineering.API.Common;
using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Features.ServiceCategories.Commands.CreateServiceCategory;
using BrilliantEngineering.Application.Features.ServiceCategories.Commands.DeleteServiceCategory;
using BrilliantEngineering.Application.Features.ServiceCategories.Commands.ReorderServiceCategories;
using BrilliantEngineering.Application.Features.ServiceCategories.Commands.UpdateServiceCategory;
using BrilliantEngineering.Application.Features.ServiceCategories.Queries.GetAllServiceCategories;
using BrilliantEngineering.Application.Features.ServiceCategories.Queries.GetServiceCategoryById;
using MediatR;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;

namespace BrilliantEngineering.API.Controllers;

[Route("api/service-categories")]
[Authorize(Roles = "Admin")]
public class ServiceCategoriesController : BaseController
{
    public ServiceCategoriesController(IMediator mediator) : base(mediator)
    {
    }

    [HttpGet]
    [AllowAnonymous]
    public async Task<IActionResult> GetAll(
        [FromQuery] bool? onlyActive,
        [FromQuery] int pageIndex = 1,
        [FromQuery] int pageSize = 10)
    {
        var result = await Mediator.Send(new GetAllServiceCategoriesQuery(onlyActive, pageIndex, pageSize));
        return OkResponse(result);
    }

    [HttpGet("{id:int}")]
    [AllowAnonymous]
    public async Task<IActionResult> GetById(int id)
    {
        var result = await Mediator.Send(new GetServiceCategoryByIdQuery(id));
        return OkResponse(result);
    }

    [HttpPost]
    public async Task<IActionResult> Create([FromBody] CreateServiceCategoryCommand command)
    {
        var result = await Mediator.Send(command);
        return CreatedResponse(result);
    }

    [HttpPut("{id:int}")]
    public async Task<IActionResult> Update(int id, [FromBody] UpdateServiceCategoryCommand command)
    {
        if (id != command.Id)
            return BadRequestResponse("Route id and body id must match.");

        var result = await Mediator.Send(command);
        return OkResponse(result, "Service category updated successfully.");
    }

    [HttpPut("Bulk")]
    public async Task<IActionResult> UpdateBulk([FromBody] List<UpdateServiceCategoryCommand> commands)
    {
        var results = new List<ServiceCategoryDto>();
        foreach (var command in commands)
        {
            var result = await Mediator.Send(command);
            results.Add(result);
        }
        return OkResponse(results, "Service categories updated successfully.");
    }

    [HttpDelete("{id:int}")]
    public async Task<IActionResult> Delete(int id)
    {
        var result = await Mediator.Send(new DeleteServiceCategoryCommand(id));
        return OkResponse(result, "Service category deleted successfully.");
    }

    [HttpPut("reorder")]
    public async Task<IActionResult> Reorder([FromBody] ReorderServiceCategoriesCommand command)
    {
        var result = await Mediator.Send(command);
        return OkResponse(result, "Service categories reordered successfully.");
    }
}
