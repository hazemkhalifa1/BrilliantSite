using BrilliantEngineering.API.Common;
using BrilliantEngineering.Application.Features.Contact.Commands.UpdateContact;
using BrilliantEngineering.Application.Features.Contact.Queries.GetContact;
using MediatR;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;

namespace BrilliantEngineering.API.Controllers;

[Route("api/contact")]
[Authorize(Roles = "Admin")]
public class ContactController : BaseController
{
    public ContactController(IMediator mediator) : base(mediator)
    {
    }

    [HttpGet]
    [AllowAnonymous]
    public async Task<IActionResult> Get()
    {
        var result = await Mediator.Send(new GetContactQuery());
        return OkResponse(result);
    }

    [HttpPut]
    public async Task<IActionResult> Update([FromBody] UpdateContactCommand command)
    {
        var result = await Mediator.Send(command);
        return OkResponse(result, "Contact info updated successfully.");
    }
}
