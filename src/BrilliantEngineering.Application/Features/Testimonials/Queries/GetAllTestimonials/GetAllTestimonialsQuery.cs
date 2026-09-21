using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Models;
using MediatR;

namespace BrilliantEngineering.Application.Features.Testimonials.Queries.GetAllTestimonials;

public record GetAllTestimonialsQuery(bool? OnlyActive, int PageIndex, int PageSize)
    : IRequest<PagedResult<TestimonialDto>>;