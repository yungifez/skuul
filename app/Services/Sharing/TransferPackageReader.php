<?php

namespace App\Services\Sharing;

use App\Enums\DataCategory;
use App\Models\TransferPackage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * Lay a received package out for the school that asked for it.
 *
 * The package is a copy another school built from its own records, so its
 * internal ids mean nothing here and are left out. Each category the request
 * named becomes one section, in the order the request named them.
 */
class TransferPackageReader
{
    /**
     * Keys whose values are codes, shown as words.
     */
    private const CODED = ['status', 'category', 'gender'];

    /**
     * The key that keeps the order of a map's keys in the stored package.
     */
    public const KEY_ORDER = '__keys';

    /**
     * Get the sections of the package.
     *
     * @param  array<int, DataCategory>  $hidden  the categories the reader may not see
     * @return list<array{label: string, fields: list<array{label: string, value: string}>, tables: list<array{label: string|null, columns: list<string>, rows: list<list<string>>}>}>
     */
    public function sections(TransferPackage $package, array $hidden = []): array
    {
        $sections = [];

        foreach ($package->categories as $value) {
            $category = DataCategory::tryFrom($value);

            if ($category === null || in_array($category, $hidden, true)) {
                continue;
            }

            $part = $package->payload[$value] ?? [];
            $sections[] = ['label' => $category->label(), ...$this->layOut(is_array($part) ? $this->inBuiltOrder($part) : [])];
        }

        return $sections;
    }

    /**
     * Put every map's keys back in the order the package was built in.
     *
     * Packages built before the order was kept come back as they are stored.
     *
     * @param  array<int|string, mixed>  $part
     * @return array<int|string, mixed>
     */
    private function inBuiltOrder(array $part): array
    {
        $order = $part[self::KEY_ORDER] ?? null;
        unset($part[self::KEY_ORDER]);

        $part = array_map(fn (mixed $value): mixed => is_array($value) ? $this->inBuiltOrder($value) : $value, $part);

        if (!is_array($order)) {
            return $part;
        }

        $ordered = [];

        foreach ($order as $key) {
            if (array_key_exists($key, $part)) {
                $ordered[$key] = $part[$key];
                unset($part[$key]);
            }
        }

        return [...$ordered, ...$part];
    }

    /**
     * Split one part into single values and tables.
     *
     * @param  array<int|string, mixed>  $part
     * @return array{fields: list<array{label: string, value: string}>, tables: list<array{label: string|null, columns: list<string>, rows: list<list<string>>}>}
     */
    private function layOut(array $part): array
    {
        if (array_is_list($part)) {
            return ['fields' => [], 'tables' => $part === [] ? [] : [$this->table(null, $part)]];
        }

        $fields = [];
        $tables = [];

        foreach ($part as $key => $value) {
            if ($this->isInternal((string) $key)) {
                continue;
            }

            if (is_array($value) && array_is_list($value) && $value !== [] && is_array($value[0])) {
                $tables[] = $this->table($this->label((string) $key), $value);

                continue;
            }

            $fields[] = ['label' => $this->label((string) $key), 'value' => $this->show((string) $key, $value)];
        }

        return ['fields' => $fields, 'tables' => $tables];
    }

    /**
     * Turn a list of rows into a table, one column per key the rows carry.
     *
     * @param  list<mixed>  $rows
     * @return array{label: string|null, columns: list<string>, rows: list<list<string>>}
     */
    private function table(?string $label, array $rows): array
    {
        $keys = collect($rows)
            ->filter(fn (mixed $row): bool => is_array($row))
            ->flatMap(fn (array $row): array => array_keys($row))
            ->map(fn (int|string $key): string => (string) $key)
            ->unique()
            ->reject(fn (string $key): bool => $this->isInternal($key))
            ->values()
            ->all();

        return [
            'label' => $label,
            'columns' => array_map($this->label(...), $keys),
            'rows' => array_map(
                fn (mixed $row): array => array_map(
                    fn (string $key): string => $this->show($key, is_array($row) ? ($row[$key] ?? null) : null),
                    $keys,
                ),
                $rows,
            ),
        ];
    }

    private function isInternal(string $key): bool
    {
        return $key === 'id' || str_ends_with($key, '_id');
    }

    private function label(string $key): string
    {
        return Str::ucfirst(str_replace('_', ' ', $key));
    }

    /**
     * Show one value as text, "—" when there is none.
     */
    private function show(string $key, mixed $value): string
    {
        return match (true) {
            $value === null, $value === '', $value === [] => '—',
            is_bool($value) => $value ? 'Yes' : 'No',
            is_float($value) => number_format($value, 2),
            is_int($value) => (string) $value,
            is_array($value) => collect($value)->map(fn (mixed $item): string => $this->show($key, $item))->implode(', '),
            is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}(?:[T ][\d:.]+(?:Z|[+-]\d{2}:?\d{2})?)?$/', $value) === 1 => Carbon::parse($value)->format('j M Y'),
            is_string($value) && in_array($key, self::CODED, true) => Str::ucfirst(str_replace('_', ' ', $value)),
            is_scalar($value) => (string) $value,
            default => '—',
        };
    }
}
