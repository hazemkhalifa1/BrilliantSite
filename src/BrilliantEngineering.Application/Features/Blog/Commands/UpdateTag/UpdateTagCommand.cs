using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.Blog.Commands.UpdateTag;

public record UpdateTagCommand(int Id, string Name) : IRequest<TagDto>;
