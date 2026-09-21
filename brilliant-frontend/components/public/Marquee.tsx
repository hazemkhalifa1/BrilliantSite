export function Marquee({ items }: { items: string[] }) {
  return (
    <div className="neo-marquee" aria-hidden="true">
      <div className="neo-marquee__track">
        <div className="neo-marquee__group">
          {items.map((item) => (
            <span key={`a-${item}`} className="neo-marquee__item">
              {item}
            </span>
          ))}
        </div>
        <div className="neo-marquee__group">
          {items.map((item) => (
            <span key={`b-${item}`} className="neo-marquee__item">
              {item}
            </span>
          ))}
        </div>
      </div>
    </div>
  );
}