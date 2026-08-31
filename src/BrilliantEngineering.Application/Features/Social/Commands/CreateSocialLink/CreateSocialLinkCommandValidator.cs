using FluentValidation;

namespace BrilliantEngineering.Application.Features.Social.Commands.CreateSocialLink;

public class CreateSocialLinkCommandValidator : AbstractValidator<CreateSocialLinkCommand>
{
    public CreateSocialLinkCommandValidator()
    {
        RuleFor(x => x.Platform).NotEmpty().MaximumLength(50);
        RuleFor(x => x.Url).NotEmpty().MaximumLength(500);
        RuleFor(x => x.IconClass).MaximumLength(100);
    }
}
