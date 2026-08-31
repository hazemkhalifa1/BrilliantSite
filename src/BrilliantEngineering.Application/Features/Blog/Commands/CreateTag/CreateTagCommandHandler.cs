using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Application.Common.Helpers;
using BrilliantEngineering.Application.Common.Specifications;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Blog.Commands.CreateTag;

public class CreateTagCommandHandler : IRequestHandler<CreateTagCommand, TagDto>
{
    private readonly IUnitOfWork _unitOfWork;

    public CreateTagCommandHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<TagDto> Handle(CreateTagCommand request, CancellationToken cancellationToken)
    {
        var repository = _unitOfWork.Repository<Tag>();

        async Task<bool> SlugExists(string slug) => await repository.CountAsync(new TagSlugExistsSpec(slug)) > 0;

        var tag = new Tag
        {
            Name = request.Name,
            Slug = SlugHelper.MakeUnique(SlugHelper.Generate(request.Name), SlugExists),
        };

        repository.Add(tag);
        await _unitOfWork.Complete();

        return tag.ToDto();
    }
}
