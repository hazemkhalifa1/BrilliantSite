using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.Meta.Queries.GetMeta;

public record GetMetaQuery(string Page, string? BaseUrl = null) : IRequest<MetaDto>;
