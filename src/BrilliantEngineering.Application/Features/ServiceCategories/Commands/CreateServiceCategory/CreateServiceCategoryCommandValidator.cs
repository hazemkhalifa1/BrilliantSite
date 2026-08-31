using FluentValidation;

namespace BrilliantEngineering.Application.Features.ServiceCategories.Commands.CreateServiceCategory;

public class CreateServiceCategoryCommandValidator : AbstractValidator<CreateServiceCategoryCommand>
{
    public CreateServiceCategoryCommandValidator()
    {
        RuleFor(x => x.Name).NotEmpty().MaximumLength(150);
        RuleFor(x => x.NameAr).MaximumLength(150);
        RuleFor(x => x.Description).MaximumLength(250);
        RuleFor(x => x.DescriptionAr).MaximumLength(250);
        RuleFor(x => x.IsActive).NotNull();
    }
}
