<?php

namespace Tests\Feature;

use Tests\TestCase;
use ZipArchive;

class ExtensionArtifactTest extends TestCase
{
    public function test_extension_manifest_and_referenced_files_are_valid(): void
    {
        $root = public_path('extension-build/infinite-sugar');
        $manifest = json_decode(file_get_contents($root.'/manifest.json'), true, flags: JSON_THROW_ON_ERROR);

        $this->assertSame(3, $manifest['manifest_version']);
        $this->assertSame('1.6.2', $manifest['version']);
        $this->assertSame('service-worker.js', $manifest['background']['service_worker']);
        $this->assertContains('https://www.infinitesugar.com/*', $manifest['host_permissions']);
        $this->assertNotContains('http://127.0.0.1:8000/*', $manifest['host_permissions']);
        $this->assertNotContains('https://mickolidia.easytechinfo.net/*', $manifest['host_permissions']);

        foreach ([
            'manifest.json',
            'service-worker.js',
            'background.js',
            'extension-config.js',
            'popup.html',
            'popup.js',
            'popup-status.js',
            'contentScript.js',
            'contentStyle.css',
            'welcome.html',
            'welcome-setup.js',
            'assets/icon.png',
        ] as $file) {
            $this->assertFileExists($root.'/'.$file);
        }

        foreach ($manifest['content_scripts'][0]['js'] as $script) {
            $this->assertFileExists($root.'/'.$script);
        }

        foreach ($manifest['content_scripts'][0]['css'] as $style) {
            $this->assertFileExists($root.'/'.$style);
        }

        foreach ($manifest['icons'] as $icon) {
            $this->assertFileExists($root.'/'.$icon);
        }
    }

    public function test_extension_build_contains_only_production_api_host(): void
    {
        $root = public_path('extension-build/infinite-sugar');
        $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($root));

        foreach ($files as $file) {
            if (! $file->isFile() || ! preg_match('/\.(js|json|html|css)$/', $file->getFilename())) {
                continue;
            }

            $contents = file_get_contents($file->getPathname());

            $this->assertStringNotContainsString('mickolidia', $contents, $file->getPathname());
            $this->assertStringNotContainsString('127.0.0.1', $contents, $file->getPathname());
            $this->assertStringNotContainsString('localhost', $contents, $file->getPathname());
        }

        $this->assertStringContainsString('https://www.infinitesugar.com/api', file_get_contents($root.'/extension-config.js'));
    }

    public function test_versioned_extension_zip_has_clean_load_unpacked_structure(): void
    {
        if (! class_exists(ZipArchive::class)) {
            $this->markTestSkipped('ZipArchive is not available.');
        }

        $archive = public_path('downloads/InfiniteSugar-Chrome-Extension-v1.6.2.zip');
        $this->assertFileExists($archive);

        $zip = new ZipArchive();
        $this->assertTrue($zip->open($archive));

        $entries = [];

        for ($index = 0; $index < $zip->numFiles; $index++) {
            $entries[] = $zip->getNameIndex($index);
        }

        $zip->close();

        $this->assertContains('manifest.json', $entries);
        $this->assertContains('service-worker.js', $entries);
        $this->assertContains('popup.html', $entries);
        $this->assertContains('contentScript.js', $entries);
        $this->assertCount(1, array_filter($entries, fn (string $entry) => basename($entry) === 'manifest.json'));

        foreach ($entries as $entry) {
            $this->assertFalse(str_starts_with($entry, 'infinite-sugar/'), $entry);
            $this->assertStringNotContainsString('node_modules/', $entry);
            $this->assertFalse(str_ends_with($entry, '.map'), $entry);
            $this->assertFalse(str_ends_with($entry, '.zip'), $entry);
        }
    }
}
