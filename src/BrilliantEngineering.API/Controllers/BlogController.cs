using BrilliantEngineering.API.Common;
using BrilliantEngineering.Application.Features.Blog.Commands.CreateBlogPost;
using BrilliantEngineering.Application.Features.Blog.Commands.DeleteBlogPost;
using BrilliantEngineering.Application.Features.Blog.Commands.PublishBlogPost;
using BrilliantEngineering.Application.Features.Blog.Commands.ReorderBlogPosts;
using BrilliantEngineering.Application.Features.Blog.Commands.UnpublishBlogPost;
using BrilliantEngineering.Application.Features.Blog.Commands.UpdateBlogPost;
using BrilliantEngineering.Application.Features.Blog.Queries.GetAllBlogPosts;
using BrilliantEngineering.Application.Features.Blog.Queries.GetBlogPostById;
using BrilliantEngineering.Application.Features.Blog.Queries.GetBlogPostBySlug;
using MediatR;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;

namespace BrilliantEngineering.API.Controllers;

[Route("api/blog")]
[Authorize(Roles = "Admin")]
public class BlogController : BaseController
{
    public BlogController(IMediator mediator) : base(mediator)
    {
    }

    [HttpGet]
    [AllowAnonymous]
    public async Task<IActionResult> GetAll(
        [FromQuery] bool? publishedOnly,
        [FromQuery] int? tagId,
        [FromQuery] int pageIndex = 1,
        [FromQuery] int pageSize = 10)
    {
        var result = await Mediator.Send(new GetAllBlogPostsQuery(publishedOnly, tagId, pageIndex, pageSize));
        return OkResponse(result);
    }

    [HttpGet("{id:int}")]
    [AllowAnonymous]
    public async Task<IActionResult> GetById(int id)
    {
        var result = await Mediator.Send(new GetBlogPostByIdQuery(id));
        return OkResponse(result);
    }

    [HttpGet("{slug}")]
    [AllowAnonymous]
    public async Task<IActionResult> GetBySlug(string slug)
    {
        var result = await Mediator.Send(new GetBlogPostBySlugQuery(slug));
        return OkResponse(result);
    }

    [HttpPost]
    public async Task<IActionResult> Create([FromBody] CreateBlogPostCommand command)
    {
        var result = await Mediator.Send(command);
        return CreatedResponse(result);
    }

    [HttpPut("{id:int}")]
    public async Task<IActionResult> Update(int id, [FromBody] UpdateBlogPostCommand command)
    {
        if (id != command.Id)
            return BadRequestResponse("Route id and body id must match.");

        var result = await Mediator.Send(command);
        return OkResponse(result, "Blog post updated successfully.");
    }

    [HttpDelete("{id:int}")]
    public async Task<IActionResult> Delete(int id)
    {
        var result = await Mediator.Send(new DeleteBlogPostCommand(id));
        return OkResponse(result, "Blog post deleted successfully.");
    }

    [HttpPost("{id:int}/publish")]
    public async Task<IActionResult> Publish(int id)
    {
        var result = await Mediator.Send(new PublishBlogPostCommand(id));
        return OkResponse(result, "Blog post published successfully.");
    }

    [HttpPost("{id:int}/unpublish")]
    public async Task<IActionResult> Unpublish(int id)
    {
        var result = await Mediator.Send(new UnpublishBlogPostCommand(id));
        return OkResponse(result, "Blog post unpublished successfully.");
    }

    [HttpPut("reorder")]
    public async Task<IActionResult> Reorder([FromBody] ReorderBlogPostsCommand command)
    {
        var result = await Mediator.Send(command);
        return OkResponse(result, "Blog posts reordered successfully.");
    }
}
