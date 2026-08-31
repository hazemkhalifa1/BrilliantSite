using BrilliantEngineering.API.Common;
using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Features.ProductBrands.Commands.CreateProductBrand;
using BrilliantEngineering.Application.Features.ProductBrands.Commands.DeleteProductBrand;
using BrilliantEngineering.Application.Features.ProductBrands.Commands.UpdateProductBrand;
using BrilliantEngineering.Application.Features.ProductBrands.Queries.GetAllProductBrands;
using BrilliantEngineering.Application.Features.ProductBrands.Queries.GetProductBrandById;
using MediatR;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;

namespace BrilliantEngineering.API.Controllers;

[Route("api/product-brands")]
[Authorize(Roles = "Admin")]
public class ProductBrandsController : BaseController
{
    public ProductBrandsController(IMediator mediator) : base(mediator)
    {
    }

    [HttpGet]
    [AllowAnonymous]
    public async Task<IActionResult> GetAll(
        [FromQuery] bool? onlyActive,
        [FromQuery] int pageIndex = 1,
        [FromQuery] int pageSize = 10)
    {
        var result = await Mediator.Send(new GetAllProductBrandsQuery(onlyActive, pageIndex, pageSize));
        return OkResponse(result);
    }

    [HttpGet("{id:int}")]
    [AllowAnonymous]
    public async Task<IActionResult> GetById(int id)
    {
        var result = await Mediator.Send(new GetProductBrandByIdQuery(id));
        return OkResponse(result);
    }

    [HttpPost]
    public async Task<IActionResult> Create([FromBody] CreateProductBrandCommand command)
    {
        var result = await Mediator.Send(command);
        return CreatedResponse(result);
    }

    [HttpPut("{id:int}")]
    public async Task<IActionResult> Update(int id, [FromBody] UpdateProductBrandCommand command)
    {
        if (id != command.Id)
            return BadRequestResponse("Route id and body id must match.");

        var result = await Mediator.Send(command);
        return OkResponse(result, "Product brand updated successfully.");
    }

    [HttpPut("Bulk")]
    public async Task<IActionResult> BulkUpdate([FromBody] List<UpdateProductBrandCommand> commands)
    {
        var results = new List<ProductBrandDto>();
        foreach (var command in commands)
        {
            var result = await Mediator.Send(command);
            results.Add(result);
        }
        return OkResponse(results, "Product brand updated successfully.");
    }

    [HttpDelete("{id:int}")]
    public async Task<IActionResult> Delete(int id)
    {
        var result = await Mediator.Send(new DeleteProductBrandCommand(id));
        return OkResponse(result, "Product brand deleted successfully.");
    }
}
