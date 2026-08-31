using System.Linq.Expressions;

namespace BrilliantEngineering.Domain.Common;

public abstract class BaseSpecification<TEntity> : ISpecification<TEntity> where TEntity : BaseEntity
{
    public Expression<Func<TEntity, bool>>? Criteria { get; private set; }

    public List<Expression<Func<TEntity, object>>> Includes { get; } = new();

    public List<string> IncludeStrings { get; } = new();

    public Expression<Func<TEntity, object>>? OrderBy { get; private set; }

    public Expression<Func<TEntity, object>>? OrderByDescending { get; private set; }

    public int Take { get; private set; }

    public int Skip { get; private set; }

    public bool IsPagingEnabled { get; private set; }

    protected void ApplyCriteria(Expression<Func<TEntity, bool>> criteria)
    {
        Criteria = Criteria is null ? criteria : AndAlso(Criteria, criteria);
    }

    protected void AddInclude(Expression<Func<TEntity, object>> includeExpression) => Includes.Add(includeExpression);

    protected void AddInclude(string includeString) => IncludeStrings.Add(includeString);

    protected void ApplyOrderBy(Expression<Func<TEntity, object>> orderByExpression) => OrderBy = orderByExpression;

    protected void ApplyOrderByDescending(Expression<Func<TEntity, object>> orderByDescendingExpression)
        => OrderByDescending = orderByDescendingExpression;

    protected void ApplyPaging(int skip, int take)
    {
        Skip = skip;
        Take = take;
        IsPagingEnabled = true;
    }

    private static Expression<Func<TEntity, bool>> AndAlso(
        Expression<Func<TEntity, bool>> left,
        Expression<Func<TEntity, bool>> right)
    {
        var parameter = Expression.Parameter(typeof(TEntity));
        var body = Expression.AndAlso(
            new ReplaceParameterVisitor(parameter).Visit(left.Body),
            new ReplaceParameterVisitor(parameter).Visit(right.Body));

        return Expression.Lambda<Func<TEntity, bool>>(body, parameter);
    }

    private sealed class ReplaceParameterVisitor : ExpressionVisitor
    {
        private readonly ParameterExpression _parameter;

        public ReplaceParameterVisitor(ParameterExpression parameter)
        {
            _parameter = parameter;
        }

        protected override Expression VisitParameter(ParameterExpression node)
            => node.Type == _parameter.Type ? _parameter : base.VisitParameter(node);
    }
}
