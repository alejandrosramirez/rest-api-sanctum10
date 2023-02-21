<?php

namespace App\Helpers;

use DeviceDetector\ClientHints;
use DeviceDetector\DeviceDetector;
use DeviceDetector\Parser\Client\Browser;
use DeviceDetector\Parser\OperatingSystem;

class Logger
{
    /**
     * Log a message
     *
     * @param  string  $logName
     * @param  string  $type
     * @param  string  $description
     * @param  ?mixed  $extraData
     */
    public static function create(string $logName, string $type, string $description, mixed $extraData = null): void
    {
        $userAgent = $_SERVER['HTTP_USER_AGENT'];

        $clientHints = ClientHints::factory($_SERVER);

        $deviceDetector = new DeviceDetector($userAgent, $clientHints);

        $os = OperatingSystem::getOsFamily($deviceDetector->getOs());
        $browser = Browser::getBrowserFamily($deviceDetector->getClient());

        $data = [
            'ip' => request()->ip(),
            'device' => $deviceDetector->getDeviceName(),
            'device_browser' => $browser,
            'device_platform' => $os,
            'type' => $type,
            'description' => $description,
        ];

        if ($extraData) {
            $data = array_merge($data, ['info' => $extraData]);
        }

        activity(strtoupper("{$logName}_log"))
            ->withProperties($data)
            ->log($description);
    }
}
