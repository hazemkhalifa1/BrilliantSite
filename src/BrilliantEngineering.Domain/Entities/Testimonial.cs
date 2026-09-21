using BrilliantEngineering.Domain.Common;

namespace BrilliantEngineering.Domain.Entities;

public class Testimonial : BaseEntity
{
    public string Name { get; set; } = string.Empty;
    public string? NameAr { get; set; }
    public string Quote { get; set; } = string.Empty;
    public string? QuoteAr { get; set; }
    public string Role { get; set; } = string.Empty;
    public string? RoleAr { get; set; }
    public string ImagePath { get; set; } = string.Empty;
    public int Order { get; set; }
    public bool IsActive { get; set; } = true;
}