using BrilliantEngineering.Application.Common.DTOs;
using MediatR;

namespace BrilliantEngineering.Application.Features.Testimonials.Queries.GetTestimonialById;

public record GetTestimonialByIdQuery(int Id) : IRequest<TestimonialDto>;