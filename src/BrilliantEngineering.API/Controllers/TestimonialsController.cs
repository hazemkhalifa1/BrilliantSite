using BrilliantEngineering.API.Common;
using BrilliantEngineering.Application.Features.Testimonials.Commands.CreateTestimonial;
using BrilliantEngineering.Application.Features.Testimonials.Commands.DeleteTestimonial;
using BrilliantEngineering.Application.Features.Testimonials.Commands.ReorderTestimonials;
using BrilliantEngineering.Application.Features.Testimonials.Commands.UpdateTestimonial;
using BrilliantEngineering.Application.Features.Testimonials.Queries.GetAllTestimonials;
using BrilliantEngineering.Application.Features.Testimonials.Queries.GetTestimonialById;
using MediatR;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;

namespace BrilliantEngineering.API.Controllers;

[Route("api/testimonials")]
[Authorize(Roles = "Admin")]
public class TestimonialsController : BaseController
{
    public TestimonialsController(IMediator mediator) : base(mediator)
    {
    }

    [HttpGet]
    [AllowAnonymous]
    public async Task<IActionResult> GetAll(
        [FromQuery] bool? onlyActive,
        [FromQuery] int pageIndex = 1,
        [FromQuery] int pageSize = 10)
    {
        var result = await Mediator.Send(new GetAllTestimonialsQuery(onlyActive, pageIndex, pageSize));
        return OkResponse(result);
    }

    [HttpGet("{id:int}")]
    [AllowAnonymous]
    public async Task<IActionResult> GetById(int id)
    {
        var result = await Mediator.Send(new GetTestimonialByIdQuery(id));
        return OkResponse(result);
    }

    [HttpPost]
    public async Task<IActionResult> Create([FromBody] CreateTestimonialCommand command)
    {
        var result = await Mediator.Send(command);
        return CreatedResponse(result);
    }

    [HttpPut("{id:int}")]
    public async Task<IActionResult> Update(int id, [FromBody] UpdateTestimonialCommand command)
    {
        if (id != command.Id)
            return BadRequestResponse("Route id and body id must match.");

        var result = await Mediator.Send(command);
        return OkResponse(result, "Testimonial updated successfully.");
    }

    [HttpDelete("{id:int}")]
    public async Task<IActionResult> Delete(int id)
    {
        var result = await Mediator.Send(new DeleteTestimonialCommand(id));
        return OkResponse(result, "Testimonial deleted successfully.");
    }

    [HttpPut("reorder")]
    public async Task<IActionResult> Reorder([FromBody] ReorderTestimonialsCommand command)
    {
        var result = await Mediator.Send(command);
        return OkResponse(result, "Testimonials reordered successfully.");
    }
}