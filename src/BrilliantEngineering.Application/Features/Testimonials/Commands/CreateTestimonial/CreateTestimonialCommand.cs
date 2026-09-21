using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.Testimonials.Commands.CreateTestimonial;

public record CreateTestimonialCommand(
    string Name,
    string? NameAr,
    string Quote,
    string? QuoteAr,
    string Role,
    string? RoleAr,
    string ImagePath,
    bool IsActive) : IRequest<TestimonialDto>;