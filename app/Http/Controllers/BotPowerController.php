<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class BotPowerController extends Controller
{
    public function getPower()
    {
        $TOKEN = env('PTERODACTYL_TOKEN');
        $ch = curl_init('https://bothostmanager.kelvincodes.nl/api/application/servers/2s');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json' , "Authorization: Bearer $TOKEN"));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        $ptero = json_decode(curl_exec($ch), true);
        if ($ptero['attributes']['status'] == 'suspended') {
            $botUptime = "Offline";
        } else {
            $botUptime = "Online";
        }
        return view('bot.power', ['uptime' => $botUptime]);
    }
}
