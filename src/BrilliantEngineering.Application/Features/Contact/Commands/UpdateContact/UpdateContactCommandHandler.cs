using BrilliantEngineering.Application.Common;
using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Application.Common.Exceptions;
using BrilliantEngineering.Application.Common.Specifications;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Contact.Commands.UpdateContact;

public class UpdateContactCommandHandler : IRequestHandler<UpdateContactCommand, ContactDto>
{
    private readonly IUnitOfWork _unitOfWork;

    public UpdateContactCommandHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<ContactDto> Handle(UpdateContactCommand request, CancellationToken cancellationToken)
    {
        var contact = await _unitOfWork.Repository<ContactInfo>()
            .GetEntityWithSpecAsync(new SingleContactSpec())
            ?? throw new NotFoundException("Contact info was not found.");

        contact.Phone1 = request.Phone1;
        contact.Phone2 = request.Phone2;
        contact.Email = request.Email;
        contact.Email2 = request.Email2;
        contact.Address = request.Address;
        contact.AddressAr = request.AddressAr;
        contact.MapEmbedUrl = GoogleMapsUrlNormalizer.Normalize(request.MapEmbedUrl);
        contact.UpdatedAt = DateTime.UtcNow;

        _unitOfWork.Repository<ContactInfo>().Update(contact);
        await _unitOfWork.Complete();

        return contact.ToDto();
    }
}
