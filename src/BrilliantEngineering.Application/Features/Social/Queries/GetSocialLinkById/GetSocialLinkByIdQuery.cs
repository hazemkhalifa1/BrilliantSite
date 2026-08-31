using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.Social.Queries.GetSocialLinkById;

public record GetSocialLinkByIdQuery(int Id) : IRequest<SocialLinkDto>;
