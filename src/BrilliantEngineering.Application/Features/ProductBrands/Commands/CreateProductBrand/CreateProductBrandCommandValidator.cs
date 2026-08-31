using FluentValidation;

namespace BrilliantEngineering.Application.Features.ProductBrands.Commands.CreateProductBrand;

public class CreateProductBrandCommandValidator : AbstractValidator<CreateProductBrandCommand>
{
    public CreateProductBrandCommandValidator()
    {
        RuleFor(x => x.Name).NotEmpty().MaximumLength(150);
        RuleFor(x => x.NameAr).MaximumLength(150);
        RuleFor(x => x.BackgroundImagePath).MaximumLength(500);
    }
}
