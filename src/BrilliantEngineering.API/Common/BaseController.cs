using MediatR;
using Microsoft.AspNetCore.Mvc;

namespace BrilliantEngineering.API.Common;

[ApiController]
public abstract class BaseController : ControllerBase
{
    protected IMediator Mediator { get; }

    protected BaseController(IMediator mediator)
    {
        Mediator = mediator;
    }

    protected IActionResult OkResponse<T>(T data, string message = "Success")
        => Ok(ApiResponse<T>.Ok(data, message));

    protected IActionResult CreatedResponse<T>(T data, string message = "Created")
        => StatusCode(StatusCodes.Status201Created, ApiResponse<T>.Created(data, message));

    protected IActionResult SuccessResponse(string message = "Success")
        => Ok(ApiResponse<bool>.Ok(true, message));

    protected IActionResult BadRequestResponse(string message)
        => BadRequest(ApiResponse<object>.Fail(StatusCodes.Status400BadRequest, message));
}
