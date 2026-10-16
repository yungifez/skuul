<?php

namespace App\Services\Library;

use App\Models\LibraryCopy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Pagination\LengthAwarePaginator;

class LibraryCopyCatalog
{
    /**
     * Search the current campus's copies by book or copy details.
     */
    public function search(string $search): LengthAwarePaginator
    {
        $search = trim($search);

        return LibraryCopy::query()
            ->inSchool()
            ->with(['title', 'loans' => fn (Relation $loan): mixed => $loan->whereNull('returned_on')->with('borrower')])
            ->when($search !== '', function (Builder $query) use ($search): void {
                $query->where(function (Builder $match) use ($search): void {
                    $match->where('barcode', 'like', "%$search%")
                        ->orWhereHas('title', function (Builder $title) use ($search): void {
                            $title->where(function (Builder $named) use ($search): void {
                                $named->where('title', 'like', "%$search%")
                                    ->orWhere('authors', 'like', "%$search%")
                                    ->orWhere('isbn', 'like', "%$search%");
                            });
                        });
                });
            })
            ->orderBy('barcode')
            ->paginate(20)
            ->withQueryString();
    }
}
