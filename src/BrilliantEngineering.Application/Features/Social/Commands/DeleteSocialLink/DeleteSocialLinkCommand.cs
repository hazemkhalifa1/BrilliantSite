using MediatR;

namespace BrilliantEngineering.Application.Features.Social.Commands.DeleteSocialLink;

public record DeleteSocialLinkCommand(int Id) : IRequest<bool>;
