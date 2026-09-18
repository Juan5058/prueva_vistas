<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;

class SearchQuery
{
    public const MIN_LENGTH = 3;

    public const MAX_LENGTH = 80;

    public const MAX_RESULTS = 8;

    /**
     * @param  list<string>  $columns
     */
    public static function apply(Builder $query, ?string $search, array $columns): void
    {
        if (! filled($search)) {
            return;
        }

        static::constrain($query, (string) $search, $columns, prefix: false);
    }

    /**
     * Restricts a query to a small result set. Prefix matching is used for codes
     * so MongoDB can stop after MAX_RESULTS hits instead of scanning the collection.
     *
     * @param  list<string>  $columns
     */
    public static function applyLimited(Builder $query, string $term, array $columns, bool $prefix = false, int $limit = self::MAX_RESULTS): Builder
    {
        static::constrain($query, $term, $columns, $prefix);

        return $query->limit($limit);
    }

    public static function normalize(?string $search): ?string
    {
        $term = trim((string) $search);

        if ($term === '') {
            return null;
        }

        $term = mb_substr($term, 0, self::MAX_LENGTH);

        if (mb_strlen($term) < self::MIN_LENGTH) {
            return null;
        }

        return $term;
    }

    /**
     * @param  list<string>  $columns
     */
    private static function constrain(Builder $query, string $search, array $columns, bool $prefix): void
    {
        $pattern = ($prefix ? '/^' : '/').preg_quote($search, '/').'/i';

        $query->where(function (Builder $inner) use ($pattern, $columns) {
            foreach ($columns as $index => $column) {
                $inner->{$index === 0 ? 'where' : 'orWhere'}($column, 'regex', $pattern);
            }
        });
    }
}
