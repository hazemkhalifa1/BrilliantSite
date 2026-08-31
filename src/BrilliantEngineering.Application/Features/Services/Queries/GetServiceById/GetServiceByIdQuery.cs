using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.Services.Queries.GetServiceById;

public record GetServiceByIdQuery(int Id) : IRequest<ServiceDto>;
