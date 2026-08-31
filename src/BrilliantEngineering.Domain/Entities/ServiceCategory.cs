using BrilliantEngineering.Domain.Common;

namespace BrilliantEngineering.Domain.Entities;

public class ServiceCategory : BaseEntity
{
    public string Name { get; set; } = string.Empty;
    public string? NameAr { get; set; }
    public string Description { get; set; }
    public string? DescriptionAr { get; set; }
    public int Order { get; set; }
    public bool IsActive { get; set; } = true;
    public ICollection<Service> Services { get; set; } = new List<Service>();
}
