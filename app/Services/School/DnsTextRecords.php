<?php

namespace App\Services\School;

/**
 * Isolate DNS TXT lookups so domain verification can substitute test responses.
 */
class DnsTextRecords
{
    /**
     * Get the text records of one name.
     *
     * @return array<int, string>
     */
    public function lookup(string $name): array
    {
        $records = @dns_get_record($name, DNS_TXT);

        if ($records === false) {
            return [];
        }

        $values = [];

        foreach ($records as $record) {
            if (isset($record['txt']) && is_string($record['txt'])) {
                $values[] = $record['txt'];
            }
        }

        return $values;
    }
}
