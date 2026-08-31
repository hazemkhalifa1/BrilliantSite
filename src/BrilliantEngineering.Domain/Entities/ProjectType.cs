using BrilliantEngineering.Domain.Common;

namespace BrilliantEngineering.Domain.Entities;

public class ProjectType : BaseEntity
{
    public string Name { get; set; } = string.Empty;
    public string? NameAr { get; set; }
    public bool IsActive { get; set; } = true;
    public ICollection<Project> Projects { get; set; } = new List<Project>();
}
