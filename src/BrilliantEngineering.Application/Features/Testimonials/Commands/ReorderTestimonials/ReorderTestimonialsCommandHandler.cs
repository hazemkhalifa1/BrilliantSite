using BrilliantEngineering.Application.Common.Exceptions;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Testimonials.Commands.ReorderTestimonials;

public class ReorderTestimonialsCommandHandler : IRequestHandler<ReorderTestimonialsCommand, bool>
{
    private readonly IUnitOfWork _unitOfWork;

    public ReorderTestimonialsCommandHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<bool> Handle(ReorderTestimonialsCommand request, CancellationToken cancellationToken)
    {
        var repository = _unitOfWork.Repository<Testimonial>();
        var testimonials = await repository.GetAllAsync();
        var byId = testimonials.ToDictionary(t => t.Id);

        foreach (var item in request.Items)
        {
            if (!byId.TryGetValue(item.Id, out var testimonial))
                throw new NotFoundException(nameof(Testimonial), item.Id);

            testimonial.Order = item.Order;
            repository.Update(testimonial);
        }

        return await _unitOfWork.Complete() > 0;
    }
}