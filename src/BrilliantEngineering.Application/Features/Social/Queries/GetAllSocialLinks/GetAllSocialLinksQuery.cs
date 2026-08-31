using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Models;
using MediatR;

namespace BrilliantEngineering.Application.Features.Social.Queries.GetAllSocialLinks;

public record GetAllSocialLinksQuery(bool? OnlyActive, int PageIndex, int PageSize)
    : IRequest<PagedResult<SocialLinkDto>>;
