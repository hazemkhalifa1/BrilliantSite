using BrilliantEngineering.Domain.Common;
using BrilliantEngineering.Domain.Interfaces;
using BrilliantEngineering.Infrastructure.Data;
using Microsoft.EntityFrameworkCore;

namespace BrilliantEngineering.Infrastructure.Repositories;

public class GenericRepository<TEntity> : IGenericRepository<TEntity> where TEntity : BaseEntity
{
    private readonly AppDbContext _context;

    public GenericRepository(AppDbContext context)
    {
        _context = context;
    }

    public async Task<TEntity?> GetByIdAsync(int id)
        => await _context.Set<TEntity>().FindAsync(id);

    public async Task<IReadOnlyList<TEntity>> GetAllAsync()
        => await _context.Set<TEntity>().AsNoTracking().ToListAsync();

    public async Task<IReadOnlyList<TEntity>> GetAllWithSpecAsync(ISpecification<TEntity> spec)
        => await ApplySpecification(spec).AsNoTracking().ToListAsync();

    public async Task<TEntity?> GetEntityWithSpecAsync(ISpecification<TEntity> spec)
        => await ApplySpecification(spec).FirstOrDefaultAsync();

    public async Task<int> CountAsync(ISpecification<TEntity> spec)
        => await ApplySpecification(spec).CountAsync();

    public void Add(TEntity entity) => _context.Set<TEntity>().Add(entity);

    public void Update(TEntity entity) => _context.Set<TEntity>().Update(entity);

    public void Delete(TEntity entity) => _context.Set<TEntity>().Remove(entity);

    private IQueryable<TEntity> ApplySpecification(ISpecification<TEntity> spec)
        => SpecificationEvaluator<TEntity>.GetQuery(_context.Set<TEntity>(), spec);
}
