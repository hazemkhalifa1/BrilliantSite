using BrilliantEngineering.Domain.Common;

namespace BrilliantEngineering.Domain.Entities;

public class HeroSection : BaseEntity
{
    public string HeadlineTop { get; set; } = string.Empty;
    public string? HeadlineTopAr { get; set; }
    public string HeadlineBottom { get; set; } = string.Empty;
    public string? HeadlineBottomAr { get; set; }
    public string SubText { get; set; } = string.Empty;
    public string? SubTextAr { get; set; }
    public string PrimaryBtnText { get; set; } = string.Empty;
    public string? PrimaryBtnTextAr { get; set; }
    public string PrimaryBtnUrl { get; set; } = string.Empty;
    public string SecondaryBtnText { get; set; } = string.Empty;
    public string? SecondaryBtnTextAr { get; set; }
    public string SecondaryBtnUrl { get; set; } = string.Empty;
    public DateTime UpdatedAt { get; set; }
}
