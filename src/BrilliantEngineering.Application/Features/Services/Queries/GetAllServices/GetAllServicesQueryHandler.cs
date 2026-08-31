using BrilliantEngineering.Application.Common.DTOs;
using BrilliantEngineering.Application.Common.Mappings;
using BrilliantEngineering.Application.Common.Models;
using BrilliantEngineering.Application.Common.Specifications;
using BrilliantEngineering.Domain.Entities;
using BrilliantEngineering.Domain.Interfaces;
using MediatR;

namespace BrilliantEngineering.Application.Features.Services.Queries.GetAllServices;

public class GetAllServicesQueryHandler : IRequestHandler<GetAllServicesQuery, PagedResult<ServiceDto>>
{
    private readonly IUnitOfWork _unitOfWork;

    public GetAllServicesQueryHandler(IUnitOfWork unitOfWork)
    {
        _unitOfWork = unitOfWork;
    }

    public async Task<PagedResult<ServiceDto>> Handle(GetAllServicesQuery request, CancellationToken cancellationToken)
    {
        var repository = _unitOfWork.Repository<Service>();

        var spec = new PagedServicesWithCategorySpec(request.CategoryId, request.OnlyActive, request.Search, request.PageIndex, request.PageSize);
        var countSpec = new ServicesWithCategorySpec(request.CategoryId, request.OnlyActive, request.Search);

        var services = await repository.GetAllWithSpecAsync(spec);
        var totalCount = await repository.CountAsync(countSpec);

        var items = services.ToDtoList();

        return PagedResult<ServiceDto>.Create(items, totalCount, request.PageIndex, request.PageSize);
    }
}
