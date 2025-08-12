<?php

namespace App\Traits;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

trait HasPaginatedResponse
{
    protected int $perPage = 15;

    protected function paginateIndex(Request $request, Builder $query): array
    {
        if ($request->has('get_all') && $request->input('get_all') === 'true') {
            $data = $query->get();
            return [
                'data' => $data,
                'pagination_meta' => null,
            ];
        }

        $perPage = $request->input('per_page', $this->perPage);
        $data = $query->paginate($perPage);

        return [
            'data' => $data->items(),
            'pagination_meta' => $this->paginationParams($data),
        ];
    }

    public function paginationParams(LengthAwarePaginator $paginator): array
    {
        return [
            'pagination' => [
                'total' => $paginator->total(),
                'per_page' => $paginator->perPage(),
                'current_page' => $paginator->currentPage(),
                'last_page' => $paginator->lastPage(),
                'from' => $paginator->firstItem(),
                'to' => $paginator->lastItem(),
            ],
        ];
    }
}
