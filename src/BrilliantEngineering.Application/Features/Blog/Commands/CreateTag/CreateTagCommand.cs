using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.Blog.Commands.CreateTag;

public record CreateTagCommand(string Name) : IRequest<TagDto>;
