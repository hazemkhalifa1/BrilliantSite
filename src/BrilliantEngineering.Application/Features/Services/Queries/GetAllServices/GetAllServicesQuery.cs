using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Models;
using MediatR;

namespace BrilliantEngineering.Application.Features.Services.Queries.GetAllServices;

public record GetAllServicesQuery(int? CategoryId, bool? OnlyActive, string? Search, int PageIndex, int PageSize)
    : IRequest<PagedResult<ServiceDto>>;
