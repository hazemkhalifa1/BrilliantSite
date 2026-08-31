using BrilliantEngineering.Domain.Common;

namespace BrilliantEngineering.Domain.Entities;

public class HeroStat : BaseEntity
{
    public string Value { get; set; } = string.Empty;
    public string Label { get; set; } = string.Empty;
    public string? LabelAr { get; set; }
    public int Order { get; set; }
    public bool IsActive { get; set; } = true;
}
