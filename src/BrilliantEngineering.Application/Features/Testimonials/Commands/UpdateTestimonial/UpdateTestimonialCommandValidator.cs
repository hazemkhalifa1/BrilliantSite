using FluentValidation;

namespace BrilliantEngineering.Application.Features.Testimonials.Commands.UpdateTestimonial;

public class UpdateTestimonialCommandValidator : AbstractValidator<UpdateTestimonialCommand>
{
    public UpdateTestimonialCommandValidator()
    {
        RuleFor(x => x.Id).GreaterThan(0);
        RuleFor(x => x.Name).NotEmpty().MaximumLength(150);
        RuleFor(x => x.NameAr).MaximumLength(150);
        RuleFor(x => x.Quote).NotEmpty().MaximumLength(2000);
        RuleFor(x => x.QuoteAr).MaximumLength(2000);
        RuleFor(x => x.Role).MaximumLength(200);
        RuleFor(x => x.RoleAr).MaximumLength(200);
        RuleFor(x => x.ImagePath).MaximumLength(500);
    }
}