using BrilliantEngineering.Domain.Common;

namespace BrilliantEngineering.Domain.Interfaces;

public interface IGenericRepository<TEntity> where TEntity : BaseEntity
{
    Task<TEntity?> GetByIdAsync(int id);
    Task<IReadOnlyList<TEntity>> GetAllAsync();
    Task<IReadOnlyList<TEntity>> GetAllWithSpecAsync(ISpecification<TEntity> spec);
    Task<TEntity?> GetEntityWithSpecAsync(ISpecification<TEntity> spec);
    Task<int> CountAsync(ISpecification<TEntity> spec);
    void Add(TEntity entity);
    void Update(TEntity entity);
    void Delete(TEntity entity);
}
