using BrilliantEngineering.Application.Common.Exceptions;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Testimonials.Commands.DeleteTestimonial;

public class DeleteTestimonialCommandHandler : IRequestHandler<DeleteTestimonialCommand, bool>
{
    private readonly IUnitOfWork _unitOfWork;

    public DeleteTestimonialCommandHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<bool> Handle(DeleteTestimonialCommand request, CancellationToken cancellationToken)
    {
        var testimonial = await _unitOfWork.Repository<Testimonial>().GetByIdAsync(request.Id);
        if (testimonial is null)
            throw new NotFoundException(nameof(Testimonial), request.Id);

        _unitOfWork.Repository<Testimonial>().Delete(testimonial);
        return await _unitOfWork.Complete() > 0;
    }
}