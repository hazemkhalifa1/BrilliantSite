using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.Social.Commands.UpdateSocialLink;

public record UpdateSocialLinkCommand(
    int Id,
    string Platform,
    string Url,
    string IconClass,
    bool IsActive) : IRequest<SocialLinkDto>;
