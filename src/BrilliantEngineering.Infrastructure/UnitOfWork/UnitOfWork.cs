using System.Collections;
using BrilliantEngineering.Domain.Common;
using BrilliantEngineering.Domain.Interfaces;
using BrilliantEngineering.Infrastructure.Data;
using BrilliantEngineering.Infrastructure.Repositories;

namespace BrilliantEngineering.Infrastructure.Data;

public class UnitOfWork : IUnitOfWork
{
    private readonly AppDbContext _context;
    private Hashtable? _repositories;

    public UnitOfWork(AppDbContext context)
    {
        _context = context;
    }

    public async Task<int> Complete() => await _context.SaveChangesAsync();

    public IGenericRepository<TEntity> Repository<TEntity>() where TEntity : BaseEntity
    {
        _repositories ??= new Hashtable();

        var type = typeof(TEntity).Name;
        if (_repositories.ContainsKey(type))
            return (IGenericRepository<TEntity>)_repositories[type]!;

        var repositoryType = typeof(GenericRepository<>);
        var repository = Activator.CreateInstance(repositoryType.MakeGenericType(typeof(TEntity)), _context);
        _repositories.Add(type, repository);

        return (IGenericRepository<TEntity>)repository!;
    }

    public async ValueTask DisposeAsync() => await _context.DisposeAsync();
}
