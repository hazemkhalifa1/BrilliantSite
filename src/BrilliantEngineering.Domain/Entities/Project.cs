using BrilliantEngineering.Domain.Common;

namespace BrilliantEngineering.Domain.Entities;

public class Project : BaseEntity
{
    public string Title { get; set; } = string.Empty;
    public string? TitleAr { get; set; }
    public string Description { get; set; } = string.Empty;
    public string? DescriptionAr { get; set; }
    public string ImagePath { get; set; } = string.Empty;
    public string ClientName { get; set; } = string.Empty;
    public string? ClientNameAr { get; set; }
    public int Year { get; set; }
    public int TypeId { get; set; }
    public ProjectType? Type { get; set; }
    public int Order { get; set; }
    public bool IsActive { get; set; } = true;
}
