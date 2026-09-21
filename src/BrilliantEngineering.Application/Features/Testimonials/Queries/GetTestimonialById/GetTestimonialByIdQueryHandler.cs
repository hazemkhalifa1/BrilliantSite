using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Exceptions;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Testimonials.Queries.GetTestimonialById;

public class GetTestimonialByIdQueryHandler : IRequestHandler<GetTestimonialByIdQuery, TestimonialDto>
{
    private readonly IUnitOfWork _unitOfWork;

    public GetTestimonialByIdQueryHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<TestimonialDto> Handle(GetTestimonialByIdQuery request, CancellationToken cancellationToken)
    {
        var testimonial = await _unitOfWork.Repository<Testimonial>().GetByIdAsync(request.Id);
        if (testimonial is null)
            throw new NotFoundException(nameof(Testimonial), request.Id);

        return testimonial.ToDto();
    }
}