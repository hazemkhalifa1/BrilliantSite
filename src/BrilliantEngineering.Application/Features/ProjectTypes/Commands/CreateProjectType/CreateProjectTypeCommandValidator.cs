using FluentValidation;

namespace BrilliantEngineering.Application.Features.ProjectTypes.Commands.CreateProjectType;

public class CreateProjectTypeCommandValidator : AbstractValidator<CreateProjectTypeCommand>
{
    public CreateProjectTypeCommandValidator()
    {
        RuleFor(x => x.Name).NotEmpty().MaximumLength(150);
        RuleFor(x => x.NameAr).MaximumLength(150);
    }
}
