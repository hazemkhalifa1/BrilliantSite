using FluentValidation;

namespace BrilliantEngineering.Application.Features.Social.Commands.UpdateSocialLink;

public class UpdateSocialLinkCommandValidator : AbstractValidator<UpdateSocialLinkCommand>
{
    public UpdateSocialLinkCommandValidator()
    {
        RuleFor(x => x.Id).GreaterThan(0);
        RuleFor(x => x.Platform).NotEmpty().MaximumLength(50);
        RuleFor(x => x.Url).NotEmpty().MaximumLength(500);
        RuleFor(x => x.IconClass).MaximumLength(100);
    }
}
