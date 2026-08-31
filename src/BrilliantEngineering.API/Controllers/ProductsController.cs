using BrilliantEngineering.API.Common;
using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Features.Products.Commands.CreateProduct;
using BrilliantEngineering.Application.Features.Products.Commands.DeleteProduct;
using BrilliantEngineering.Application.Features.Products.Commands.UpdateProduct;
using BrilliantEngineering.Application.Features.Products.Queries.GetAllProducts;
using BrilliantEngineering.Application.Features.Products.Queries.GetProductById;
using MediatR;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;

namespace BrilliantEngineering.API.Controllers;

[Route("api/products")]
[Authorize(Roles = "Admin")]
public class ProductsController : BaseController
{
    public ProductsController(IMediator mediator) : base(mediator)
    {
    }

    [HttpGet]
    [AllowAnonymous]
    public async Task<IActionResult> GetAll(
        [FromQuery] int? brandId,
        [FromQuery] int? categoryId,
        [FromQuery] bool? onlyActive,
        [FromQuery] string? search,
        [FromQuery] int pageIndex = 1,
        [FromQuery] int pageSize = 10)
    {
        var result = await Mediator.Send(new GetAllProductsQuery(brandId, categoryId, onlyActive, search, pageIndex, pageSize));
        return OkResponse(result);
    }

    [HttpGet("{id:int}")]
    [AllowAnonymous]
    public async Task<IActionResult> GetById(int id)
    {
        var result = await Mediator.Send(new GetProductByIdQuery(id));
        return OkResponse(result);
    }

    [HttpPost]
    public async Task<IActionResult> Create([FromBody] CreateProductCommand command)
    {
        var result = await Mediator.Send(command);
        return CreatedResponse(result);
    }

    [HttpPost("Bulk")]
    public async Task<IActionResult> CreateBulk([FromBody] IEnumerable<CreateProductCommand> commands)
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
    public async Task<IActionResult> Update(int id, [FromBody] UpdateProductCommand command)
    {
        if (id != command.Id)
            return BadRequestResponse("Route id and body id must match.");

        var result = await Mediator.Send(command);
        return OkResponse(result, "Product updated successfully.");
    }

    [HttpPut("Bulk")]
    public async Task<IActionResult> UpdateBulk([FromBody] List<UpdateProductCommand> commands)
    {
        var results = new List<ProductDto>();
        foreach (var command in commands)
        {
            var result = await Mediator.Send(command);
            results.Add(result);
        }
        return OkResponse(results, "Products updated successfully.");
    }

    [HttpDelete("{id:int}")]
    public async Task<IActionResult> Delete(int id)
    {
        var result = await Mediator.Send(new DeleteProductCommand(id));
        return OkResponse(result, "Product deleted successfully.");
    }
}
