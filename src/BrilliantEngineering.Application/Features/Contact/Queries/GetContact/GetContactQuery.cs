using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.Contact.Queries.GetContact;

public record GetContactQuery : IRequest<ContactDto>;
