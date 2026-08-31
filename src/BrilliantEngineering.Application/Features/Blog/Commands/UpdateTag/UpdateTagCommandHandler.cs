using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Application.Common.Exceptions;
using BrilliantEngineering.Application.Common.Helpers;
using BrilliantEngineering.Application.Common.Specifications;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Blog.Commands.UpdateTag;

public class UpdateTagCommandHandler : IRequestHandler<UpdateTagCommand, TagDto>
{
    private readonly IUnitOfWork _unitOfWork;

    public UpdateTagCommandHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<TagDto> Handle(UpdateTagCommand request, CancellationToken cancellationToken)
    {
        var repository = _unitOfWork.Repository<Tag>();

        var tag = await repository.GetByIdAsync(request.Id);
        if (tag is null)
            throw new NotFoundException(nameof(Tag), request.Id);

        async Task<bool> SlugExists(string slug) => await repository.CountAsync(new TagSlugExistsSpec(slug, request.Id)) > 0;

        tag.Name = request.Name;
        tag.Slug = SlugHelper.MakeUnique(SlugHelper.Generate(request.Name), SlugExists);

        repository.Update(tag);
        await _unitOfWork.Complete();

        return tag.ToDto();
    }
}
