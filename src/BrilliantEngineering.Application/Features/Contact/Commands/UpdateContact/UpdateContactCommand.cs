using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.Contact.Commands.UpdateContact;

public record UpdateContactCommand(
    string Phone1,
    string Phone2,
    string Email,
    string Email2,
    string Address,
    string? AddressAr,
    string MapEmbedUrl) : IRequest<ContactDto>;
