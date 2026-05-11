<?php

namespace App\Support;

class ForgeReportFixture
{
    public static function load(string $filename): array
    {
        $path = database_path("seeders/data/{$filename}");
        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new \RuntimeException("Unable to load Forge report fixture [{$filename}].");
        }

        $payload = json_decode($contents, true);

        if (! is_array($payload)) {
            throw new \RuntimeException("Forge report fixture [{$filename}] is invalid.");
        }

        return $payload;
    }
}
