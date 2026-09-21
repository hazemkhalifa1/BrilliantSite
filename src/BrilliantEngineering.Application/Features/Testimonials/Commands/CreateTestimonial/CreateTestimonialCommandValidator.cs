using FluentValidation;

namespace BrilliantEngineering.Application.Features.Testimonials.Commands.CreateTestimonial;

public class CreateTestimonialCommandValidator : AbstractValidator<CreateTestimonialCommand>
{
    public CreateTestimonialCommandValidator()
    {
        RuleFor(x => x.Name).NotEmpty().MaximumLength(150);
        RuleFor(x => x.NameAr).MaximumLength(150);
        RuleFor(x => x.Quote).NotEmpty().MaximumLength(2000);
        RuleFor(x => x.QuoteAr).MaximumLength(2000);
        RuleFor(x => x.Role).MaximumLength(200);
        RuleFor(x => x.RoleAr).MaximumLength(200);
        RuleFor(x => x.ImagePath).MaximumLength(500);
    }
}