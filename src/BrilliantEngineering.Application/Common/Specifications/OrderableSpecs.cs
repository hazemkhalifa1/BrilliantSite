using BrilliantEngineering.Domain.Common;
using BrilliantEngineering.Domain.Entities;

namespace BrilliantEngineering.Application.Common.Specifications;

public class TeamMembersOrderedSpec : BaseSpecification<TeamMember>
{
    public TeamMembersOrderedSpec(bool? onlyActive)
    {
        ApplyOrderBy(t => t.Order);

        if (onlyActive == true)
            ApplyCriteria(t => t.IsActive);
    }
}

public class PagedTeamMembersOrderedSpec : TeamMembersOrderedSpec
{
    public PagedTeamMembersOrderedSpec(bool? onlyActive, int pageIndex, int pageSize)
        : base(onlyActive)
    {
        ApplyPaging((pageIndex - 1) * pageSize, pageSize);
    }
}

public class TestimonialsOrderedSpec : BaseSpecification<Testimonial>
{
    public TestimonialsOrderedSpec(bool? onlyActive)
    {
        ApplyOrderBy(t => t.Order);

        if (onlyActive == true)
            ApplyCriteria(t => t.IsActive);
    }
}

public class PagedTestimonialsOrderedSpec : TestimonialsOrderedSpec
{
    public PagedTestimonialsOrderedSpec(bool? onlyActive, int pageIndex, int pageSize)
        : base(onlyActive)
    {
        ApplyPaging((pageIndex - 1) * pageSize, pageSize);
    }
}

public class ClientsOrderedSpec : BaseSpecification<Client>
{
    public ClientsOrderedSpec(bool? onlyActive)
    {
        ApplyOrderBy(c => c.Order);

        if (onlyActive == true)
            ApplyCriteria(c => c.IsActive);
    }
}

public class PagedClientsOrderedSpec : ClientsOrderedSpec
{
    public PagedClientsOrderedSpec(bool? onlyActive, int pageIndex, int pageSize)
        : base(onlyActive)
    {
        ApplyPaging((pageIndex - 1) * pageSize, pageSize);
    }
}

public class SocialLinksOrderedSpec : BaseSpecification<SocialLink>
{
    public SocialLinksOrderedSpec(bool? onlyActive)
    {
        ApplyOrderBy(s => s.Order);

        if (onlyActive == true)
            ApplyCriteria(s => s.IsActive);
    }
}

public class PagedSocialLinksOrderedSpec : SocialLinksOrderedSpec
{
    public PagedSocialLinksOrderedSpec(bool? onlyActive, int pageIndex, int pageSize)
        : base(onlyActive)
    {
        ApplyPaging((pageIndex - 1) * pageSize, pageSize);
    }
}

public class HeroStatsOrderedSpec : BaseSpecification<HeroStat>
{
    public HeroStatsOrderedSpec(bool? onlyActive)
    {
        ApplyOrderBy(h => h.Order);

        if (onlyActive == true)
            ApplyCriteria(h => h.IsActive);
    }
}

public class PagedHeroStatsOrderedSpec : HeroStatsOrderedSpec
{
    public PagedHeroStatsOrderedSpec(bool? onlyActive, int pageIndex, int pageSize)
        : base(onlyActive)
    {
        ApplyPaging((pageIndex - 1) * pageSize, pageSize);
    }
}

public class SingleHeroSpec : BaseSpecification<HeroSection>
{
    public SingleHeroSpec()
    {
        ApplyOrderBy(h => h.Id);
    }
}

public class SingleContactSpec : BaseSpecification<ContactInfo>
{
    public SingleContactSpec()
    {
        ApplyOrderBy(c => c.Id);
    }
}
