using FluentValidation;

namespace BrilliantEngineering.Application.Features.Team.Commands.CreateTeamMember;

public class CreateTeamMemberCommandValidator : AbstractValidator<CreateTeamMemberCommand>
{
    public CreateTeamMemberCommandValidator()
    {
        RuleFor(x => x.Name).NotEmpty().MaximumLength(150);
        RuleFor(x => x.NameAr).MaximumLength(150);
        RuleFor(x => x.JobTitle).MaximumLength(200);
        RuleFor(x => x.JobTitleAr).MaximumLength(200);
        RuleFor(x => x.ImagePath).MaximumLength(500);
    }
}
