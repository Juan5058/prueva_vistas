<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;

class SearchQuery
{
    /**
     * @param  list<string>  $columns
     */
    public static function apply(Builder $query, ?string $search, array $columns): void
    {
        if (! filled($search)) {
            return;
        }

        $query->where(function (Builder $inner) use ($search, $columns) {
            foreach ($columns as $index => $column) {
                $inner->{$index === 0 ? 'where' : 'orWhere'}($column, 'regex', '/'.preg_quote($search, '/').'/i');
            }
        });
    }
}
