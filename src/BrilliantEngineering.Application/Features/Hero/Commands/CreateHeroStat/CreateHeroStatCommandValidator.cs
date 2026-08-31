using FluentValidation;

namespace BrilliantEngineering.Application.Features.Hero.Commands.CreateHeroStat;

public class CreateHeroStatCommandValidator : AbstractValidator<CreateHeroStatCommand>
{
    public CreateHeroStatCommandValidator()
    {
        RuleFor(x => x.Value).NotEmpty().MaximumLength(50);
        RuleFor(x => x.Label).NotEmpty().MaximumLength(150);
        RuleFor(x => x.LabelAr).MaximumLength(150);
    }
}
