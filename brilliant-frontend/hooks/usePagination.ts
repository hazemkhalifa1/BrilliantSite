import { useCallback, useState } from "react";

export function usePagination(initialPage = 1, initialPageSize = 10) {
  const [pageIndex, setPageIndex] = useState(initialPage);
  const [pageSize, setPageSize] = useState(initialPageSize);

  const totalPages = useCallback(
    (totalCount: number) => Math.max(1, Math.ceil(totalCount / pageSize)),
    [pageSize],
  );

  const canNext = useCallback(
    (totalCount: number) => pageIndex * pageSize < totalCount,
    [pageIndex, pageSize],
  );

  return { pageIndex, pageSize, setPageIndex, setPageSize, totalPages, canNext };
}
