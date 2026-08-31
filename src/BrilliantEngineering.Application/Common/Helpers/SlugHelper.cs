using System.Text;

namespace BrilliantEngineering.Application.Common.Helpers;

public static class SlugHelper
{
    public static string Generate(string value)
    {
        if (string.IsNullOrWhiteSpace(value))
            return string.Empty;

        var normalized = value.Trim().ToLowerInvariant();
        var builder = new StringBuilder(normalized.Length);

        foreach (var ch in normalized)
        {
            if (char.IsLetterOrDigit(ch))
            {
                builder.Append(ch);
            }
            else if (ch is ' ' or '-' or '_' or '.' or '/')
            {
                builder.Append('-');
            }
        }

        var slug = builder.ToString();
        while (slug.Contains("--", StringComparison.Ordinal))
            slug = slug.Replace("--", "-", StringComparison.Ordinal);

        return slug.Trim('-');
    }

    public static string MakeUnique(string baseSlug, Func<string, Task<bool>> existsAsync)
    {
        var candidate = baseSlug;
        var suffix = 2;

        while (!string.IsNullOrEmpty(candidate) && existsAsync(candidate).GetAwaiter().GetResult())
        {
            candidate = $"{baseSlug}-{suffix}";
            suffix++;
        }

        return candidate;
    }
}
