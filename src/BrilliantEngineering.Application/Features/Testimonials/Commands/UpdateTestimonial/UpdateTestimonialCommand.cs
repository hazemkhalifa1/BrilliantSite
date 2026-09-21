using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.Testimonials.Commands.UpdateTestimonial;

public record UpdateTestimonialCommand(
    int Id,
    string Name,
    string? NameAr,
    string Quote,
    string? QuoteAr,
    string Role,
    string? RoleAr,
    string ImagePath,
    bool IsActive) : IRequest<TestimonialDto>;