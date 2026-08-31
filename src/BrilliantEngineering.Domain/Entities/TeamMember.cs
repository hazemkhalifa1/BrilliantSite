using BrilliantEngineering.Domain.Common;

namespace BrilliantEngineering.Domain.Entities;

public class TeamMember : BaseEntity
{
    public string Name { get; set; } = string.Empty;
    public string? NameAr { get; set; }
    public string JobTitle { get; set; } = string.Empty;
    public string? JobTitleAr { get; set; }
    public string ImagePath { get; set; } = string.Empty;
    public int Order { get; set; }
    public bool IsActive { get; set; } = true;
}
