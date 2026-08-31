using BrilliantEngineering.Application.Common.Exceptions;
using FluentValidation;
using Microsoft.EntityFrameworkCore;

namespace BrilliantEngineering.API.Common;

public class ApiExceptionMiddleware
{
    private readonly RequestDelegate _next;
    private readonly ILogger<ApiExceptionMiddleware> _logger;

    public ApiExceptionMiddleware(RequestDelegate next, ILogger<ApiExceptionMiddleware> logger)
    {
        _next = next;
        _logger = logger;
    }

    public async Task InvokeAsync(HttpContext context)
    {
        try
        {
            await _next(context);
        }
        catch (Exception exception)
        {
            if (context.Response.HasStarted)
                throw;

            await HandleExceptionAsync(context, exception);
        }
    }

    private async Task HandleExceptionAsync(HttpContext context, Exception exception)
    {
        var statusCode = StatusCodes.Status500InternalServerError;
        var message = "An unexpected error occurred.";
        object? data = null;

        switch (exception)
        {
            case NotFoundException:
                statusCode = StatusCodes.Status404NotFound;
                message = exception.Message;
                break;

            case ValidationException validationException:
                statusCode = StatusCodes.Status400BadRequest;
                message = "One or more validation errors occurred.";
                data = validationException.Errors
                    .Select(e => new { e.PropertyName, e.ErrorMessage })
                    .ToList();
                break;

            case UnauthorizedAccessException:
                statusCode = StatusCodes.Status401Unauthorized;
                message = exception.Message;
                break;

            case DbUpdateException:
                statusCode = StatusCodes.Status400BadRequest;
                message = "A database conflict occurred while saving changes.";
                _logger.LogError(exception, "Database update failed");
                break;

            default:
                _logger.LogError(exception, "Unhandled exception during request");
                break;
        }

        context.Response.StatusCode = statusCode;
        context.Response.ContentType = "application/json";

        await context.Response.WriteAsJsonAsync(ApiResponse<object>.Fail(statusCode, message, data));
    }
}

public static class ApiExceptionMiddlewareExtensions
{
    public static IApplicationBuilder UseApiExceptionMiddleware(this IApplicationBuilder builder)
        => builder.UseMiddleware<ApiExceptionMiddleware>();
}
