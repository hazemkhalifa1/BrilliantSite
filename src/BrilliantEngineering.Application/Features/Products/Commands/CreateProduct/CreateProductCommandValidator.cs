using FluentValidation;

namespace BrilliantEngineering.Application.Features.Products.Commands.CreateProduct;

public class CreateProductCommandValidator : AbstractValidator<CreateProductCommand>
{
    public CreateProductCommandValidator()
    {
        RuleFor(x => x.Name).NotEmpty().MaximumLength(200);
        RuleFor(x => x.NameAr).MaximumLength(200);
        RuleFor(x => x.Description).MaximumLength(2000);
        RuleFor(x => x.DescriptionAr).MaximumLength(2000);
        RuleFor(x => x.CategoryId).GreaterThan(0);
        RuleFor(x => x.ImagePath).MaximumLength(500);
        RuleFor(x => x.DocumentationUrl).MaximumLength(500);
    }
}
