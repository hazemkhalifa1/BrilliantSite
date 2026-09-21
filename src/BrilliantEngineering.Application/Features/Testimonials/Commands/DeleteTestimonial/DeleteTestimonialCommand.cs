using MediatR;

namespace BrilliantEngineering.Application.Features.Testimonials.Commands.DeleteTestimonial;

public record DeleteTestimonialCommand(int Id) : IRequest<bool>;