using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.Social.Commands.CreateSocialLink;

public record CreateSocialLinkCommand(
    string Platform,
    string Url,
    string IconClass,
    bool IsActive) : IRequest<SocialLinkDto>;
