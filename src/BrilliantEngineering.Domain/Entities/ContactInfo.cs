using BrilliantEngineering.Domain.Common;

namespace BrilliantEngineering.Domain.Entities;

public class ContactInfo : BaseEntity
{
    public string Phone1 { get; set; } = string.Empty;
    public string Phone2 { get; set; } = string.Empty;
    public string Email { get; set; } = string.Empty;
    public string Email2 { get; set; } = string.Empty;
    public string Address { get; set; } = string.Empty;
    public string? AddressAr { get; set; }
    public string MapEmbedUrl { get; set; } = string.Empty;
    public DateTime UpdatedAt { get; set; }
}
