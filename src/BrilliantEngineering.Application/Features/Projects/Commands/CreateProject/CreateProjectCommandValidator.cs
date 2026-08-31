using FluentValidation;

namespace BrilliantEngineering.Application.Features.Projects.Commands.CreateProject;

public class CreateProjectCommandValidator : AbstractValidator<CreateProjectCommand>
{
    public CreateProjectCommandValidator()
    {
        RuleFor(x => x.Title).NotEmpty().MaximumLength(200);
        RuleFor(x => x.TitleAr).MaximumLength(200);
        RuleFor(x => x.Description).MaximumLength(2000);
        RuleFor(x => x.DescriptionAr).MaximumLength(2000);
        RuleFor(x => x.ClientName).MaximumLength(200);
        RuleFor(x => x.ClientNameAr).MaximumLength(200);
        RuleFor(x => x.Year).InclusiveBetween(1900, 2100);
        RuleFor(x => x.TypeId).GreaterThan(0);
        RuleFor(x => x.ImagePath).MaximumLength(500);
    }
}
