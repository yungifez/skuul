<?php

namespace App\Services\School;

/**
 * Read the text records of a name in the domain name system.
 *
 * An organization proves it owns an address by putting a value the application
 * gave it into a record only the owner of that address can write. This class
 * is the one place that asks, so a test can answer instead.
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
