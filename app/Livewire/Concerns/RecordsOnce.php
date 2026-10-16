<?php

namespace App\Livewire\Concerns;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;
use Livewire\Component;
use Throwable;

/**
 * Write a record once, however often the same save reaches the server.
 *
 * A second press, a retry after a lost answer, or a stale tab sends the save
 * again. The screen names the record it is writing with a key, and a key that
 * was already used writes nothing. Money screens use this, so one payment is
 * never taken twice.
 *
 * @mixin Component
 */
trait RecordsOnce
{
    /**
     * Name the record this screen is writing.
     */
    #[Locked]
    public string $recordKey = '';

    public function mountRecordsOnce(): void
    {
        $this->startNextRecord();
    }

    /**
     * Run the write unless this screen already wrote its record.
     *
     * A write that fails frees the key, so the user can correct the form and
     * save again.
     *
     * @template TResult
     *
     * @param  string  $purpose  what is written, so two kinds of record never share a key
     * @param  callable(): TResult  $write
     * @return TResult|null the write's result, or null when the record was already written
     */
    protected function recordOnce(string $purpose, callable $write): mixed
    {
        $key = $this->recordOnceCacheKey($purpose);

        if (!Cache::add($key, true, now()->addHour())) {
            return null;
        }

        try {
            return $write();
        } catch (Throwable $exception) {
            Cache::forget($key);

            throw $exception;
        }
    }

    /**
     * Give the screen a new key, for a screen that stays open to write another record.
     */
    protected function startNextRecord(): void
    {
        $this->recordKey = (string) Str::uuid();
    }

    /**
     * Get the cache key that marks this screen's record as written.
     */
    protected function recordOnceCacheKey(string $purpose): string
    {
        return "recorded:$purpose:".current_school_id().':'.$this->recordKey;
    }
}
