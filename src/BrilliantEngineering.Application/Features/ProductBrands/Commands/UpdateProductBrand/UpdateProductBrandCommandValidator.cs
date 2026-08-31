using FluentValidation;

namespace BrilliantEngineering.Application.Features.ProductBrands.Commands.UpdateProductBrand;

public class UpdateProductBrandCommandValidator : AbstractValidator<UpdateProductBrandCommand>
{
    public UpdateProductBrandCommandValidator()
    {
        RuleFor(x => x.Id).GreaterThan(0);
        RuleFor(x => x.Name).NotEmpty().MaximumLength(150);
        RuleFor(x => x.NameAr).MaximumLength(150);
        RuleFor(x => x.BackgroundImagePath).MaximumLength(500);
    }
}
