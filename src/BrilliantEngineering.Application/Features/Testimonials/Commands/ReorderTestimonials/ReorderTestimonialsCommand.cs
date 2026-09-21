using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.Testimonials.Commands.ReorderTestimonials;

public record ReorderTestimonialsCommand(IReadOnlyList<ReorderItemDto> Items) : IRequest<bool>;