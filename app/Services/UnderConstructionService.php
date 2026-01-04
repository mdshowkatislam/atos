<?php
namespace App\Services;

class UnderConstructionService
{
    public static function show($title, $message, $badge = 'Development Mode')
    {
        echo view('under_construction', compact('title', 'message', 'badge'))->render();
        exit();
    }
}