<?php

namespace App\Services\Orders;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class SellerSearch
{
    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator<int, User>
     */
    public function paginate(?string $query, int $perPage = 12)
    {
        return $this->baseQuery($query)
            ->with([
                'sellerSubservices.subservice.service',
            ])
            ->orderBy('name')
            ->paginate($perPage)
            ->withQueryString();
    }

    public function findSeller(int $id): ?User
    {
        return $this->baseQuery(null)
            ->whereKey($id)
            ->with(['sellerSubservices.subservice.service'])
            ->first();
    }

    /**
     * @return Collection<int, User>
     */
    public function suggest(?string $query, int $limit = 8): Collection
    {
        if ($query === null || trim($query) === '') {
            return collect();
        }

        return $this->baseQuery($query)
            ->with(['sellerSubservices.subservice'])
            ->limit($limit)
            ->get();
    }

    protected function baseQuery(?string $query): Builder
    {
        $builder = User::query()->marketplaceSellers();

        $term = $query !== null ? trim($query) : '';
        if ($term !== '') {
            $like = '%'.$term.'%';
            $builder->where(function (Builder $q) use ($like) {
                $q->where('name', 'like', $like)
                    ->orWhere('user_code', 'like', $like)
                    ->orWhere('company_name', 'like', $like)
                    ->orWhere('email', 'like', $like)
                    ->orWhere('city', 'like', $like)
                    ->orWhereHas('sellerSubservices.subservice', function (Builder $sub) use ($like) {
                        $sub->where('name', 'like', $like)
                            ->orWhereHas('service', fn (Builder $svc) => $svc->where('name', 'like', $like));
                    });
            });
        }

        return $builder;
    }
}
