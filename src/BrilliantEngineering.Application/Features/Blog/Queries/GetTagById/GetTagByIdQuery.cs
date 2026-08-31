using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.Blog.Queries.GetTagById;

public record GetTagByIdQuery(int Id) : IRequest<TagDto>;
