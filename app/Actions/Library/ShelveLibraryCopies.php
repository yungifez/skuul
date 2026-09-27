<?php

namespace App\Actions\Library;

use App\Exceptions\InvalidValueException;
use App\Models\LibraryCopy;
use App\Models\LibraryTitle;
use App\Models\School;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Put one or more copies of a book on a campus's shelf.
 *
 * Asking for several copies numbers them from the barcode that was typed, so
 * nobody types twenty of them. Every barcode is checked before any copy is
 * made, so a clash never leaves half a box on the shelf.
 */
class ShelveLibraryCopies
{
    /**
     * @param  array{title: string, authors: ?string, isbn: ?string, category: ?string}|null  $newTitle
     * @return Collection<int, LibraryCopy>
     *
     * @throws InvalidValueException when a barcode is taken or the book is already described
     */
    public function shelve(
        School $school,
        ?LibraryTitle $title,
        ?array $newTitle,
        string $barcode,
        int $copies,
        ?string $shelfMark,
    ): Collection {
        return DB::transaction(function () use ($school, $title, $newTitle, $barcode, $copies, $shelfMark): Collection {
            $barcodes = collect(range(0, $copies - 1))
                ->map(fn (int $number): string => $number === 0 ? $barcode : "{$barcode}-{$number}");

            if (mb_strlen((string) $barcodes->last()) > 60) {
                throw new InvalidValueException('The barcode is too long to number '.$copies.' copies from. Use a shorter one.');
            }

            $taken = LibraryCopy::query()
                ->where('school_id', $school->id)
                ->whereIn('barcode', $barcodes)
                ->lockForUpdate()
                ->pluck('barcode');

            if ($taken->isNotEmpty()) {
                throw new InvalidValueException('This campus already has a copy with the barcode '.$taken->sort()->implode(', ').'.');
            }

            $title ??= $this->describe($school, $newTitle ?? throw new InvalidValueException('Say which book this is.'));

            return $barcodes->map(fn (string $code): LibraryCopy => LibraryCopy::create([
                'school_id' => $school->id,
                'library_title_id' => $title->id,
                'barcode' => $code,
                'shelf_mark' => $shelfMark,
            ]));
        });
    }

    /**
     * Describe a new book for the school group.
     *
     * @param  array{title: string, authors: ?string, isbn: ?string, category: ?string}  $newTitle
     *
     * @throws InvalidValueException when the group already describes a book with that ISBN
     */
    private function describe(School $school, array $newTitle): LibraryTitle
    {
        if ($newTitle['isbn'] !== null) {
            $existing = LibraryTitle::forSchool($school)->where('isbn', $newTitle['isbn'])->first();

            if ($existing !== null) {
                throw new InvalidValueException("The catalogue already has this ISBN as \"{$existing->title}\". Pick it instead.");
            }
        }

        return LibraryTitle::create([
            'organization_id' => $school->organization_id,
            'title' => $newTitle['title'],
            'authors' => $newTitle['authors'],
            'isbn' => $newTitle['isbn'],
            'category' => $newTitle['category'],
        ]);
    }
}
