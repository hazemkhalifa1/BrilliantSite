using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Models;
using MediatR;

namespace BrilliantEngineering.Application.Features.Blog.Queries.GetAllTags;

public record GetAllTagsQuery(int PageIndex, int PageSize) : IRequest<PagedResult<TagDto>>;
