using MediatR;

namespace BrilliantEngineering.Application.Features.Blog.Commands.DeleteTag;

public record DeleteTagCommand(int Id) : IRequest<bool>;
