using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Exceptions;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Testimonials.Commands.UpdateTestimonial;

public class UpdateTestimonialCommandHandler : IRequestHandler<UpdateTestimonialCommand, TestimonialDto>
{
    private readonly IUnitOfWork _unitOfWork;

    public UpdateTestimonialCommandHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<TestimonialDto> Handle(UpdateTestimonialCommand request, CancellationToken cancellationToken)
    {
        var testimonial = await _unitOfWork.Repository<Testimonial>().GetByIdAsync(request.Id);
        if (testimonial is null)
            throw new NotFoundException(nameof(Testimonial), request.Id);

        testimonial.Name = request.Name;
        testimonial.NameAr = request.NameAr;
        testimonial.Quote = request.Quote;
        testimonial.QuoteAr = request.QuoteAr;
        testimonial.Role = request.Role;
        testimonial.RoleAr = request.RoleAr;
        testimonial.ImagePath = request.ImagePath;
        testimonial.IsActive = request.IsActive;

        _unitOfWork.Repository<Testimonial>().Update(testimonial);
        await _unitOfWork.Complete();

        return testimonial.ToDto();
    }
}