using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Testimonials.Commands.CreateTestimonial;

public class CreateTestimonialCommandHandler : IRequestHandler<CreateTestimonialCommand, TestimonialDto>
{
    private readonly IUnitOfWork _unitOfWork;

    public CreateTestimonialCommandHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<TestimonialDto> Handle(CreateTestimonialCommand request, CancellationToken cancellationToken)
    {
        var testimonials = await _unitOfWork.Repository<Testimonial>().GetAllAsync();
        var nextOrder = testimonials.Count == 0 ? 0 : testimonials.Max(t => t.Order) + 1;

        var testimonial = new Testimonial
        {
            Name = request.Name,
            NameAr = request.NameAr,
            Quote = request.Quote,
            QuoteAr = request.QuoteAr,
            Role = request.Role,
            RoleAr = request.RoleAr,
            ImagePath = request.ImagePath,
            Order = nextOrder,
            IsActive = request.IsActive,
        };

        _unitOfWork.Repository<Testimonial>().Add(testimonial);
        await _unitOfWork.Complete();

        return testimonial.ToDto();
    }
}