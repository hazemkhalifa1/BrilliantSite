using FluentValidation;

namespace BrilliantEngineering.Application.Features.Services.Commands.UpdateService;

public class UpdateServiceCommandValidator : AbstractValidator<UpdateServiceCommand>
{
    public UpdateServiceCommandValidator()
    {
        RuleFor(x => x.Id).GreaterThan(0);
        RuleFor(x => x.Title).NotEmpty().MaximumLength(200);
        RuleFor(x => x.TitleAr).MaximumLength(200);
        RuleFor(x => x.Description).MaximumLength(2000);
        RuleFor(x => x.DescriptionAr).MaximumLength(2000);
        RuleFor(x => x.CategoryId).GreaterThan(0);
        RuleFor(x => x.IconPath).MaximumLength(500);
        RuleFor(x => x.RelatedBlogPostId).GreaterThan(0).When(x => x.RelatedBlogPostId.HasValue);
    }
}
