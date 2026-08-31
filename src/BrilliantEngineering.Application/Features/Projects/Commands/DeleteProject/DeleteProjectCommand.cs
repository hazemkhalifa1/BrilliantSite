using MediatR;

namespace BrilliantEngineering.Application.Features.Projects.Commands.DeleteProject;

public record DeleteProjectCommand(int Id) : IRequest<bool>;
