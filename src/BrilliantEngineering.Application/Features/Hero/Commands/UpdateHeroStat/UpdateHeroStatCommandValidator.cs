using FluentValidation;

namespace BrilliantEngineering.Application.Features.Hero.Commands.UpdateHeroStat;

public class UpdateHeroStatCommandValidator : AbstractValidator<UpdateHeroStatCommand>
{
    public UpdateHeroStatCommandValidator()
    {
        RuleFor(x => x.Id).GreaterThan(0);
        RuleFor(x => x.Value).NotEmpty().MaximumLength(50);
        RuleFor(x => x.Label).NotEmpty().MaximumLength(150);
        RuleFor(x => x.LabelAr).MaximumLength(150);
    }
}
