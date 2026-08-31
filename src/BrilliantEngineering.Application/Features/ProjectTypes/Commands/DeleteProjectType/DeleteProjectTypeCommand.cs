using MediatR;

namespace BrilliantEngineering.Application.Features.ProjectTypes.Commands.DeleteProjectType;

public record DeleteProjectTypeCommand(int Id) : IRequest<bool>;
