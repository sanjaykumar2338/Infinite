<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ExtensionController extends Controller
{
    public const VERSION = '1.6.2';

    public const FILENAME = 'InfiniteSugar-Chrome-Extension-v1.6.2.zip';

    public function show(): View
    {
        $path = $this->archivePath();

        return view('pages.extension', [
            'extensionVersion' => self::VERSION,
            'extensionFilename' => self::FILENAME,
            'extensionAvailable' => file_exists($path),
            'extensionSize' => file_exists($path) ? filesize($path) : null,
        ]);
    }

    public function download(Request $request): BinaryFileResponse|RedirectResponse
    {
        $archive = $this->archivePath();

        if (! file_exists($archive)) {
            return redirect()
                ->route('extension.show')
                ->with('error', 'The extension download is not available yet. Please try again shortly.');
        }

        return response()
            ->download($archive, self::FILENAME, [
                'Cache-Control' => 'private, no-store, no-cache, must-revalidate, max-age=0',
                'Pragma' => 'no-cache',
                'Expires' => '0',
            ]);
    }

    private function archivePath(): string
    {
        return public_path('downloads/'.self::FILENAME);
    }
}
