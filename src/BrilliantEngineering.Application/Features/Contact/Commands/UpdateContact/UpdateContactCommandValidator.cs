using FluentValidation;

namespace BrilliantEngineering.Application.Features.Contact.Commands.UpdateContact;

public class UpdateContactCommandValidator : AbstractValidator<UpdateContactCommand>
{
    public UpdateContactCommandValidator()
    {
        RuleFor(x => x.Phone1).MaximumLength(50);
        RuleFor(x => x.Phone2).MaximumLength(50);
        RuleFor(x => x.Email)
            .MaximumLength(200)
            .When(x => !string.IsNullOrEmpty(x.Email))
            .EmailAddress()
            .When(x => !string.IsNullOrEmpty(x.Email));
        RuleFor(x => x.Email2)
            .MaximumLength(200)
            .When(x => !string.IsNullOrEmpty(x.Email2))
            .EmailAddress()
            .When(x => !string.IsNullOrEmpty(x.Email2));
        RuleFor(x => x.Address).MaximumLength(500);
        RuleFor(x => x.AddressAr).MaximumLength(500);
        RuleFor(x => x.MapEmbedUrl).MaximumLength(1000);
    }
}
