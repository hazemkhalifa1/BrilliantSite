using BrilliantEngineering.Application.Common.Exceptions;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Blog.Commands.ReorderBlogPosts;

public class ReorderBlogPostsCommandHandler : IRequestHandler<ReorderBlogPostsCommand, bool>
{
    private readonly IUnitOfWork _unitOfWork;

    public ReorderBlogPostsCommandHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<bool> Handle(ReorderBlogPostsCommand request, CancellationToken cancellationToken)
    {
        var repository = _unitOfWork.Repository<BlogPost>();
        var posts = await repository.GetAllAsync();
        var byId = posts.ToDictionary(p => p.Id);

        foreach (var item in request.Items)
        {
            if (!byId.TryGetValue(item.Id, out var post))
                throw new NotFoundException(nameof(BlogPost), item.Id);

            post.Order = item.Order;
            repository.Update(post);
        }

        return await _unitOfWork.Complete() > 0;
    }
}
