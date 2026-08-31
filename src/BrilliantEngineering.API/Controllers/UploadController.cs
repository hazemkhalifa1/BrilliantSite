using System.Text.RegularExpressions;
using BrilliantEngineering.API.Common;
using Microsoft.AspNetCore.Authorization;
using Microsoft.AspNetCore.Mvc;

namespace BrilliantEngineering.API.Controllers;

[Route("api/upload")]
[ApiController]
[Authorize(Roles = "Admin")]
public class UploadController : ControllerBase
{
    private const long MaxFileSize = 5 * 1024 * 1024;

    private static readonly string[] AllowedImageExtensions = { ".jpg", ".jpeg", ".png", ".webp" };
    private static readonly string[] AllowedImageContentTypes = { "image/jpeg", "image/png", "image/webp" };
    private static readonly string[] AllowedDocumentExtensions = { ".pdf", ".doc", ".docx", ".xls", ".xlsx", ".ppt", ".pptx", ".zip" };

    private readonly IWebHostEnvironment _environment;

    public UploadController(IWebHostEnvironment environment)
    {
        _environment = environment;
    }

    [HttpPost("image/{entity}")]
    [RequestSizeLimit(MaxFileSize)]
    public async Task<IActionResult> UploadImage(string entity, IFormFile file)
    {
        var folder = SanitizeFolder(entity);
        if (string.IsNullOrWhiteSpace(folder))
            return BadRequestResponse("Invalid entity folder.");

        if (file is null || file.Length == 0)
            return BadRequestResponse("No file was provided.");

        if (file.Length > MaxFileSize)
            return BadRequestResponse("File exceeds the maximum allowed size of 5 MB.");

        var extension = Path.GetExtension(file.FileName).ToLowerInvariant();
        if (!AllowedImageExtensions.Contains(extension) || !AllowedImageContentTypes.Contains(file.ContentType.ToLowerInvariant()))
            return BadRequestResponse("Only JPG, JPEG, PNG and WEBP images are allowed.");

        var relativePath = await SaveFileAsync(file, Path.Combine("images", folder));
        return CreatedResponse(relativePath, "Image uploaded successfully.");
    }

    [HttpPost("document")]
    [RequestSizeLimit(MaxFileSize)]
    public async Task<IActionResult> UploadDocument(IFormFile file)
    {
        if (file is null || file.Length == 0)
            return BadRequestResponse("No file was provided.");

        if (file.Length > MaxFileSize)
            return BadRequestResponse("File exceeds the maximum allowed size of 5 MB.");

        var extension = Path.GetExtension(file.FileName).ToLowerInvariant();
        if (!AllowedDocumentExtensions.Contains(extension))
            return BadRequestResponse("This document type is not allowed.");

        var relativePath = await SaveFileAsync(file, "docs");
        return CreatedResponse(relativePath, "Document uploaded successfully.");
    }

    private IActionResult BadRequestResponse(string message)
        => BadRequest(ApiResponse<object>.Fail(StatusCodes.Status400BadRequest, message));

    private IActionResult CreatedResponse(string relativePath, string message)
        => StatusCode(StatusCodes.Status201Created, ApiResponse<string>.Created(relativePath, message));

    private async Task<string> SaveFileAsync(IFormFile file, string subFolder)
    {
        var webRoot = _environment.WebRootPath ?? Path.Combine(_environment.ContentRootPath, "wwwroot");
        var fullFolder = Path.Combine(webRoot, subFolder);
        Directory.CreateDirectory(fullFolder);

        var extension = Path.GetExtension(file.FileName).ToLowerInvariant();
        var fileName = $"{Guid.NewGuid():N}{extension}";
        var relativePath = $"/{subFolder.Replace('\\', '/')}/{fileName}";

        await using (var stream = new FileStream(Path.Combine(fullFolder, fileName), FileMode.CreateNew))
        {
            await file.CopyToAsync(stream);
        }

        return relativePath;
    }

    private static string? SanitizeFolder(string value)
        => Regex.IsMatch(value, @"^[a-zA-Z0-9_-]+$") ? value.ToLowerInvariant() : null;
}
