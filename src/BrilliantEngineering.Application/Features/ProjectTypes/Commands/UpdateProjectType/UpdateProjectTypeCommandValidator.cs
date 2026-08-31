using FluentValidation;

namespace BrilliantEngineering.Application.Features.ProjectTypes.Commands.UpdateProjectType;

public class UpdateProjectTypeCommandValidator : AbstractValidator<UpdateProjectTypeCommand>
{
    public UpdateProjectTypeCommandValidator()
    {
        RuleFor(x => x.Id).GreaterThan(0);
        RuleFor(x => x.Name).NotEmpty().MaximumLength(150);
        RuleFor(x => x.NameAr).MaximumLength(150);
    }
}
