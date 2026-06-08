<?php

namespace App\Http\Controllers;

use App\Models\PageContent;
use Illuminate\Contracts\View\View;

class PageController extends Controller
{
    public function show(string $page = 'home'): View
    {
        abort_unless(in_array($page, [
            'home',
            'pricing',
            'spark',
            'forge',
            'reports',
            'privacy',
            'terms',
            'dashboard',
        ], true), 404);

        return view("pages.$page", [
            'pageContent' => PageContent::forPage($page),
        ]);
    }
}
