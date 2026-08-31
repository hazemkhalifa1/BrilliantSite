using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Application.Common.Models;
using BrilliantEngineering.Domain.Common;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Blog.Queries.GetAllTags;

public class GetAllTagsQueryHandler : IRequestHandler<GetAllTagsQuery, PagedResult<TagDto>>
{
    private readonly IUnitOfWork _unitOfWork;

    public GetAllTagsQueryHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<PagedResult<TagDto>> Handle(GetAllTagsQuery request, CancellationToken cancellationToken)
    {
        var repository = _unitOfWork.Repository<Tag>();

        var listSpec = new TagsSpec();
        var pageSpec = new PagedTagsSpec(request.PageIndex, request.PageSize);

        var tags = await repository.GetAllWithSpecAsync(pageSpec);
        var totalCount = await repository.CountAsync(listSpec);

        var items = tags.ToDtoList();

        return PagedResult<TagDto>.Create(items, totalCount, request.PageIndex, request.PageSize);
    }
}

internal class TagsSpec : BaseSpecification<Tag>
{
    public TagsSpec()
    {
        ApplyOrderBy(t => t.Name);
    }
}

internal class PagedTagsSpec : TagsSpec
{
    public PagedTagsSpec(int pageIndex, int pageSize)
    {
        ApplyPaging((pageIndex - 1) * pageSize, pageSize);
    }
}
