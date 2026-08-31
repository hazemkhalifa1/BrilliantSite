using System.Text.RegularExpressions;

namespace BrilliantEngineering.Application.Common;

public static class GoogleMapsUrlNormalizer
{
    public static string Normalize(string? url)
    {
        if (string.IsNullOrWhiteSpace(url))
            return url ?? string.Empty;

        if (url.Contains("/maps/embed", StringComparison.OrdinalIgnoreCase))
            return url;

        var cidMatch = Regex.Match(url, "[?&]cid=(\\d+)", RegexOptions.IgnoreCase);
        if (cidMatch.Success)
            return $"https://www.google.com/maps/embed?pb=!1m3!3m2!1m1!4s{cidMatch.Groups[1].Value}";

        var qMatch = Regex.Match(url, "[?&]q=([^&#]+)", RegexOptions.IgnoreCase);
        if (qMatch.Success)
        {
            var encoded = Uri.EscapeDataString(Uri.UnescapeDataString(qMatch.Groups[1].Value))
                .Replace("%20", "+");
            return $"https://www.google.com/maps/embed?pb=!1m3!2m1!1s{encoded}!6i14";
        }

        return url;
    }
}
