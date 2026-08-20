<?php

namespace App\Utilities;

class NameParser
{
    /**
     * Parse a full name in "LASTNAME, FIRSTNAME MIDDLENAME" format
     * 
     * @param string $fullName The full name from Excel
     * @return array Array with parsed components and metadata
     */
    public static function parse(string $fullName): array
    {
        $result = [
            'full_name' => trim($fullName),
            'last_name' => null,
            'first_name' => null,
            'middle_name' => null,
            'middle_initial' => null,
            'is_parsed' => false,
            'parse_notes' => null,
        ];

        // Normalize whitespace but preserve special characters
        $normalized = preg_replace('/\s+/', ' ', trim($fullName));
        
        // Check for comma separator
        if (!str_contains($normalized, ',')) {
            $result['parse_notes'] = 'No comma separator found';
            return $result;
        }

        // Split on first comma only
        $parts = explode(',', $normalized, 2);
        
        if (count($parts) < 2) {
            $result['parse_notes'] = 'Invalid format after comma split';
            return $result;
        }

        $lastName = trim($parts[0]);
        $firstNamePart = trim($parts[1]);

        // Last name is before comma
        if (empty($lastName)) {
            $result['parse_notes'] = 'Empty last name';
            return $result;
        }

        $result['last_name'] = $lastName;

        // Parse first name and middle name from the part after comma
        $nameTokens = explode(' ', $firstNamePart);
        
        if (count($nameTokens) < 1) {
            $result['parse_notes'] = 'No first name found';
            return $result;
        }

        // First token is first name
        $firstName = $nameTokens[0];
        if (empty($firstName)) {
            $result['parse_notes'] = 'Empty first name';
            return $result;
        }

        $result['first_name'] = $firstName;

        // Remaining tokens (if any) could be middle name(s)
        if (count($nameTokens) > 1) {
            $middlePart = implode(' ', array_slice($nameTokens, 1));
            
            // Check if middle part ends with a single letter with optional period
            if (preg_match('/^(.+)\s+([A-Z])\.?$/u', $middlePart, $matches)) {
                // Has middle name + initial (e.g., "JAYCON P.")
                $result['middle_name'] = trim($matches[1]);
                $result['middle_initial'] = $matches[2];
                $result['is_parsed'] = true;
            } elseif (preg_match('/^([A-Z])\.?$/u', $middlePart)) {
                // Only middle initial (e.g., "M.")
                $result['middle_initial'] = trim($middlePart, '.');
                $result['is_parsed'] = true;
            } else {
                // Full middle name without initial (e.g., "ANDREW")
                $result['middle_name'] = $middlePart;
                $result['is_parsed'] = true;
            }
        } else {
            // No middle name
            $result['is_parsed'] = true;
        }

        return $result;
    }

    /**
     * Check if a name contains special characters that need preservation
     * 
     * @param string $name
     * @return bool
     */
    public static function hasSpecialCharacters(string $name): bool
    {
        return preg_match('/[^\p{L}\s\-\'\.]/u', $name);
    }
}
