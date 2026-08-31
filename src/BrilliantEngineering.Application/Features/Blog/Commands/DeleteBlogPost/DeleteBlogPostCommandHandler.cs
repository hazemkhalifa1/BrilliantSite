using BrilliantEngineering.Application.Common.Exceptions;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Blog.Commands.DeleteBlogPost;

public class DeleteBlogPostCommandHandler : IRequestHandler<DeleteBlogPostCommand, bool>
{
    private readonly IUnitOfWork _unitOfWork;

    public DeleteBlogPostCommandHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<bool> Handle(DeleteBlogPostCommand request, CancellationToken cancellationToken)
    {
        var blogPost = await _unitOfWork.Repository<BlogPost>().GetByIdAsync(request.Id);
        if (blogPost is null)
            throw new NotFoundException(nameof(BlogPost), request.Id);

        _unitOfWork.Repository<BlogPost>().Delete(blogPost);
        return await _unitOfWork.Complete() > 0;
    }
}
