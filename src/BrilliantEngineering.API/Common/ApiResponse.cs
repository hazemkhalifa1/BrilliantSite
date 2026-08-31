namespace BrilliantEngineering.API.Common;

public class ApiResponse<T>
{
    public bool Success { get; set; }
    public string Message { get; set; } = string.Empty;
    public T? Data { get; set; }
    public int StatusCode { get; set; }

    public static ApiResponse<T> Ok(T data, string message = "Success")
        => new() { Success = true, Message = message, Data = data, StatusCode = 200 };

    public static ApiResponse<T> Created(T data, string message = "Created")
        => new() { Success = true, Message = message, Data = data, StatusCode = 201 };

    public static ApiResponse<T> Fail(int statusCode, string message, T? data = default)
        => new() { Success = false, Message = message, Data = data, StatusCode = statusCode };
}
