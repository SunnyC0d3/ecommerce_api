<?php

namespace App\Filters\V1;

use Illuminate\Database\Eloquent\Builder;

class UserFilter extends QueryFilter
{
    protected array $sortable = [
        'name',
        'email',
        'created_at',
        'updated_at',
    ];

    public function name(string $value)
    {
        $likeStr = str_replace('*', '%', $value);
        return $this->builder->where('name', 'like', $likeStr);
    }

    public function email(string $value)
    {
        $likeStr = str_replace('*', '%', $value);
        return $this->builder->where('email', 'like', $likeStr);
    }

    public function createdAt(string $value)
    {
        $dates = explode(',', $value);

        return count($dates) > 1
            ? $this->builder->whereBetween('created_at', $dates)
            : $this->builder->whereDate('created_at', $value);
    }

    public function updatedAt(string $value)
    {
        $dates = explode(',', $value);

        return count($dates) > 1
            ? $this->builder->whereBetween('updated_at', $dates)
            : $this->builder->whereDate('updated_at', $value);
    }

    public function role(string|array $value)
    {
        $ids = is_array($value) ? $value : explode(',', $value);
        return $this->builder->whereIn('role_id', $ids);
    }

    public function include(string|array $value)
    {
        return $this->builder->with($value);
    }

    public function search(string $value)
    {
        $likeStr = str_replace('*', '%', $value);
        return $this->builder->where(function (Builder $query) use ($likeStr) {
            $query->where('name', 'like', $likeStr)
                ->orWhere('email', 'like', $likeStr);
        });
    }
}
