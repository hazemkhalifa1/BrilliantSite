using FluentValidation;

namespace BrilliantEngineering.Application.Features.Hero.Commands.UpdateHero;

public class UpdateHeroCommandValidator : AbstractValidator<UpdateHeroCommand>
{
    public UpdateHeroCommandValidator()
    {
        RuleFor(x => x.HeadlineTop).MaximumLength(200);
        RuleFor(x => x.HeadlineTopAr).MaximumLength(200);
        RuleFor(x => x.HeadlineBottom).MaximumLength(200);
        RuleFor(x => x.HeadlineBottomAr).MaximumLength(200);
        RuleFor(x => x.SubText).MaximumLength(1000);
        RuleFor(x => x.SubTextAr).MaximumLength(1000);
        RuleFor(x => x.PrimaryBtnText).MaximumLength(100);
        RuleFor(x => x.PrimaryBtnTextAr).MaximumLength(100);
        RuleFor(x => x.PrimaryBtnUrl).MaximumLength(500);
        RuleFor(x => x.SecondaryBtnText).MaximumLength(100);
        RuleFor(x => x.SecondaryBtnTextAr).MaximumLength(100);
        RuleFor(x => x.SecondaryBtnUrl).MaximumLength(500);
    }
}
