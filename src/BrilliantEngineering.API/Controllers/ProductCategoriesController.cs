using BrilliantEngineering.API.Common;
using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Features.ProductCategories.Commands.CreateProductCategory;
using BrilliantEngineering.Application.Features.ProductCategories.Commands.DeleteProductCategory;
using BrilliantEngineering.Application.Features.ProductCategories.Commands.UpdateProductCategory;
using BrilliantEngineering.Application.Features.ProductCategories.Queries.GetAllProductCategories;
using BrilliantEngineering.Application.Features.ProductCategories.Queries.GetProductCategoryById;
using MediatR;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;

namespace BrilliantEngineering.API.Controllers;

[Route("api/product-categories")]
[Authorize(Roles = "Admin")]
public class ProductCategoriesController : BaseController
{
    public ProductCategoriesController(IMediator mediator) : base(mediator)
    {
    }

    [HttpGet]
    [AllowAnonymous]
    public async Task<IActionResult> GetAll(
        [FromQuery] int? brandId,
        [FromQuery] bool? onlyActive,
        [FromQuery] int pageIndex = 1,
        [FromQuery] int pageSize = 10)
    {
        var result = await Mediator.Send(new GetAllProductCategoriesQuery(brandId, onlyActive, pageIndex, pageSize));
        return OkResponse(result);
    }

    [HttpGet("{id:int}")]
    [AllowAnonymous]
    public async Task<IActionResult> GetById(int id)
    {
        var result = await Mediator.Send(new GetProductCategoryByIdQuery(id));
        return OkResponse(result);
    }

    [HttpPost]
    public async Task<IActionResult> Create([FromBody] CreateProductCategoryCommand command)
    {
        var result = await Mediator.Send(command);
        return CreatedResponse(result);
    }

    [HttpPut("{id:int}")]
    public async Task<IActionResult> Update(int id, [FromBody] UpdateProductCategoryCommand command)
    {
        if (id != command.Id)
            return BadRequestResponse("Route id and body id must match.");

        var result = await Mediator.Send(command);
        return OkResponse(result, "Product category updated successfully.");
    }

    [HttpPut("Bulk")]
    public async Task<IActionResult> UpdateBulk([FromBody] List<UpdateProductCategoryCommand> commands)
    {
        var results = new List<ProductCategoryDto>();
        foreach (var command in commands)
        {
            var result = await Mediator.Send(command);
            results.Add(result);
        }
        return OkResponse(results, "Product categories updated successfully.");
    }

    [HttpDelete("{id:int}")]
    public async Task<IActionResult> Delete(int id)
    {
        var result = await Mediator.Send(new DeleteProductCategoryCommand(id));
        return OkResponse(result, "Product category deleted successfully.");
    }
}
