<?php

namespace App\Support;

class LegacyPermissions
{
    /**
     * Normalize a permissions input (json, csv, array) into a flat array of strings.
     *
     * @return list<string>
     */
    public static function normalize(mixed $value): array
    {
        if ($value === null) {
            return [];
        }

        if (is_string($value)) {
            $decoded = json_decode($value, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $value = $decoded;
            } else {
                $value = preg_split('/[\s,]+/', $value, -1, PREG_SPLIT_NO_EMPTY);
            }
        } elseif ($value instanceof \JsonSerializable) {
            $value = $value->jsonSerialize();
        } elseif ($value instanceof \Traversable) {
            $value = iterator_to_array($value);
        }

        if (! is_array($value)) {
            return [];
        }

        $collected = [];
        $walker = function ($item) use (&$collected, &$walker): void {
            if (is_string($item) && trim($item) !== '') {
                $collected[] = trim($item);
                return;
            }

            if ($item === true || $item === 1) {
                $collected[] = 'true';
                return;
            }

            if (is_array($item)) {
                foreach ($item as $nested) {
                    $walker($nested);
                }
            }
        };

        foreach ($value as $item) {
            $walker($item);
        }

        $collected = array_filter($collected, static fn ($perm) => $perm !== '');

        return array_values(array_unique($collected));
    }

    /**
     * Determine if any of the expected permissions exist.
     *
     * @param  list<string>  $haystack
     * @param  list<string>  $needles
     */
    public static function containsAny(array $haystack, array $needles): bool
    {
        if ($haystack === [] || $needles === []) {
            return false;
        }

        $indexed = array_fill_keys($haystack, true);

        foreach ($needles as $needle) {
            if ($needle !== '' && isset($indexed[$needle])) {
                return true;
            }
        }

        return false;
    }
}
