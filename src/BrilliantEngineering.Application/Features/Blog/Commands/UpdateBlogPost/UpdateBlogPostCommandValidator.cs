using FluentValidation;

namespace BrilliantEngineering.Application.Features.Blog.Commands.UpdateBlogPost;

public class UpdateBlogPostCommandValidator : AbstractValidator<UpdateBlogPostCommand>
{
    public UpdateBlogPostCommandValidator()
    {
        RuleFor(x => x.Id).GreaterThan(0);
        RuleFor(x => x.Title).NotEmpty().MaximumLength(250);
        RuleFor(x => x.TitleAr).MaximumLength(250);
        RuleFor(x => x.Content).NotEmpty();
        RuleFor(x => x.ContentAr).MaximumLength(2000);
        RuleFor(x => x.CoverImagePath).MaximumLength(500);
        RuleFor(x => x.MetaTitle).MaximumLength(200);
        RuleFor(x => x.MetaTitleAr).MaximumLength(200);
        RuleFor(x => x.MetaDescription).MaximumLength(500);
        RuleFor(x => x.MetaDescriptionAr).MaximumLength(500);
        RuleFor(x => x.TagIds).NotNull();
    }
}
