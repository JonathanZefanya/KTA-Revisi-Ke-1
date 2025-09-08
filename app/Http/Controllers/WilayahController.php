<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class WilayahController extends Controller
{
    /**
     * Provinces from wilayah.id (cached 6h).
     */
    public function provinces()
    {
        $ttl = now()->addHours(6);
        $data = Cache::remember('wilayah:provinces', $ttl, function () {
            try {
                $resp = Http::timeout(10)->get('https://wilayah.id/api/provinces.json');
                if ($resp->failed()) return [];
                $json = $resp->json();
                $list = isset($json['data']) && is_array($json['data']) ? $json['data'] : (is_array($json) ? $json : []);
                return collect($list)->map(function ($p) {
                    $code = $p['code'] ?? $p['id'] ?? null;
                    $name = isset($p['name']) ? preg_replace('/\s+/', ' ', trim($p['name'])) : null;
                    return $code && $name ? ['code' => (string)$code, 'name' => $name] : null;
                })->filter()->values()->all();
            } catch (\Throwable $e) {
                return [];
            }
        });
        return response()->json(['data' => $data]);
    }

    /**
     * Regencies (kab/kota) by province code (cached 6h).
     */
    public function regencies(string $provinceCode)
    {
        $provinceCode = preg_replace('/[^0-9]/', '', $provinceCode);
        if ($provinceCode === '') {
            return response()->json(['data' => []], 400);
        }
        $cacheKey = 'wilayah:regencies:' . $provinceCode;
        $ttl = now()->addHours(6);
        $data = Cache::remember($cacheKey, $ttl, function () use ($provinceCode) {
            try {
                $resp = Http::timeout(10)->get("https://wilayah.id/api/regencies/{$provinceCode}.json");
                if ($resp->failed()) return [];
                $json = $resp->json();
                $list = isset($json['data']) && is_array($json['data']) ? $json['data'] : (is_array($json) ? $json : []);
                return collect($list)->map(function ($c) {
                    $code = $c['code'] ?? $c['id'] ?? null;
                    $name = isset($c['name']) ? preg_replace('/\s+/', ' ', trim(Str::title(strtolower($c['name'])))) : null;
                    return $code && $name ? ['code' => (string)$code, 'name' => $name] : null;
                })->filter()->values()->all();
            } catch (\Throwable $e) {
                return [];
            }
        });
        return response()->json(['data' => $data]);
    }
}
