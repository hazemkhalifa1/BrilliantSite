using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Application.Common.Models;
using BrilliantEngineering.Application.Common.Specifications;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Testimonials.Queries.GetAllTestimonials;

public class GetAllTestimonialsQueryHandler : IRequestHandler<GetAllTestimonialsQuery, PagedResult<TestimonialDto>>
{
    private readonly IUnitOfWork _unitOfWork;

    public GetAllTestimonialsQueryHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<PagedResult<TestimonialDto>> Handle(GetAllTestimonialsQuery request, CancellationToken cancellationToken)
    {
        var repository = _unitOfWork.Repository<Testimonial>();

        var spec = new PagedTestimonialsOrderedSpec(request.OnlyActive, request.PageIndex, request.PageSize);
        var countSpec = new TestimonialsOrderedSpec(request.OnlyActive);

        var testimonials = await repository.GetAllWithSpecAsync(spec);
        var totalCount = await repository.CountAsync(countSpec);

        var items = testimonials.ToDtoList();

        return PagedResult<TestimonialDto>.Create(items, totalCount, request.PageIndex, request.PageSize);
    }
}