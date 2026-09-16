<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SettingsController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $systemInfo = [
            'version' => config('app.version', '1.0.0'),
            'php_version' => PHP_VERSION,
            'laravel_version' => App::version(),
            'db_name' => config('database.connections.mysql.database'),
        ];

        return view('settings.index', compact('user', 'systemInfo'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'theme' => 'required|in:light,dark,system',
            'notifications_enabled' => 'sometimes|boolean',
        ]);

        $user = auth()->user();

        $user->update([
            'theme' => $request->theme,
            'notifications_enabled' => $request->boolean('notifications_enabled'),
        ]);

        return redirect()->route('settings.index')->with('success', 'Settings updated successfully.');
    }
}
