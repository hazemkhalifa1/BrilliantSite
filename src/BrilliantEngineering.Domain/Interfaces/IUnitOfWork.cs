using BrilliantEngineering.Domain.Common;

namespace BrilliantEngineering.Domain.Interfaces;

public interface IUnitOfWork : IAsyncDisposable
{
    Task<int> Complete();
    IGenericRepository<TEntity> Repository<TEntity>() where TEntity : BaseEntity;
}
