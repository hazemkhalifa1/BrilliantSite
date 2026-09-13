using FluentValidation;

namespace BrilliantEngineering.Application.Features.Team.Commands.UpdateTeamMember;

public class UpdateTeamMemberCommandValidator : AbstractValidator<UpdateTeamMemberCommand>
{
    public UpdateTeamMemberCommandValidator()
    {
        RuleFor(x => x.Id).GreaterThan(0);
        RuleFor(x => x.Name).NotEmpty().MaximumLength(150);
        RuleFor(x => x.NameAr).MaximumLength(150);
        RuleFor(x => x.JobTitle).MaximumLength(200);
        RuleFor(x => x.JobTitleAr).MaximumLength(200);
        RuleFor(x => x.Description).MaximumLength(2000);
        RuleFor(x => x.DescriptionAr).MaximumLength(2000);
        RuleFor(x => x.ImagePath).MaximumLength(500);
    }
}
