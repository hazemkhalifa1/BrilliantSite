using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Application.Common.Exceptions;
using BrilliantEngineering.Application.Common.Specifications;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Contact.Queries.GetContact;

public class GetContactQueryHandler : IRequestHandler<GetContactQuery, ContactDto>
{
    private readonly IUnitOfWork _unitOfWork;

    public GetContactQueryHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<ContactDto> Handle(GetContactQuery request, CancellationToken cancellationToken)
    {
        var contact = await _unitOfWork.Repository<ContactInfo>()
            .GetEntityWithSpecAsync(new SingleContactSpec());

        if (contact is null)
            throw new NotFoundException("Contact info was not found.");

        return contact.ToDto();
    }
}
