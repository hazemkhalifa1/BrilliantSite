using BrilliantEngineering.Application.Common.Exceptions;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Blog.Commands.DeleteTag;

public class DeleteTagCommandHandler : IRequestHandler<DeleteTagCommand, bool>
{
    private readonly IUnitOfWork _unitOfWork;

    public DeleteTagCommandHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<bool> Handle(DeleteTagCommand request, CancellationToken cancellationToken)
    {
        var tag = await _unitOfWork.Repository<Tag>().GetByIdAsync(request.Id);
        if (tag is null)
            throw new NotFoundException(nameof(Tag), request.Id);

        _unitOfWork.Repository<Tag>().Delete(tag);
        return await _unitOfWork.Complete() > 0;
    }
}
