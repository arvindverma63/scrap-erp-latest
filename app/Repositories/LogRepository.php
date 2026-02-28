<?php

namespace App\Repositories;

use App\Models\Log;
use Illuminate\Support\Facades\Auth;

class LogRepository
{
    /**
     * Store login information (without Agent)
     */
    public function logLogin($request): Log
    {
        $userAgent = $request->header('User-Agent');

        return Log::create([
            'user_id'    => Auth::id(),
            'ip_address' => $request->ip(),
            'user_agent' => $userAgent,
            'device'     => $this->detectDevice($userAgent),
            'browser'    => $this->detectBrowser($userAgent),
            'platform'   => $this->detectPlatform($userAgent),
            'login_at'   => now(),
            'status'     => 'Logged In',
        ]);
    }

    /**
     * Update the latest login log with logout time
     */
    public function logLogout(): ?Log
    {
        $log = Log::where('user_id', Auth::id())
            ->whereNull('logout_at')
            ->latest('login_at')
            ->first();

        if ($log) {
            $log->update([
                'logout_at' => now(),
                'status' => 'Logged Out',
            ]);
        }

        return $log;
    }

    /**
     * Simple device detection
     */
    private function detectDevice($userAgent)
    {
        if (preg_match('/mobile/i', $userAgent)) {
            return 'Mobile';
        } elseif (preg_match('/tablet/i', $userAgent)) {
            return 'Tablet';
        } else {
            return 'Desktop';
        }
    }

    /**
     * Simple browser detection
     */
    private function detectBrowser($userAgent)
    {
        $browsers = ['Edge', 'Chrome', 'Safari', 'Firefox', 'Opera', 'MSIE', 'Trident'];
        foreach ($browsers as $browser) {
            if (stripos($userAgent, $browser) !== false) {
                return $browser === 'MSIE' || $browser === 'Trident' ? 'Internet Explorer' : $browser;
            }
        }
        return 'Unknown';
    }

    /**
     * Simple platform detection
     */
    private function detectPlatform($userAgent)
    {
        $platforms = [
            'Windows' => 'Windows',
            'Macintosh' => 'MacOS',
            'Linux' => 'Linux',
            'Android' => 'Android',
            'iPhone' => 'iOS',
            'iPad' => 'iOS'
        ];

        foreach ($platforms as $key => $value) {
            if (stripos($userAgent, $key) !== false) {
                return $value;
            }
        }

        return 'Unknown';
    }
}
