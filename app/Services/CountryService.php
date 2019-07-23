<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class CountryService
{
    const STATS_KEY  = 'stats.countries';
    const PARENT_KEY = 'countries:';
    const KEY_MASK   = self::PARENT_KEY . '%s';

    /**
     * @return array
     */
    public function list()
    {
        // Если есть в кэше - забираем стату оттуда, региулируем время жизни с помощью TTL в конфиге (как бы слейв)
        if ($stats = Cache::get(self::STATS_KEY)) {
            return $stats;
        }
        // Если в кэше нет - собираем по ключам их хранилища
        $keys      = Redis::keys(self::PARENT_KEY . '*');
        $countries = [];
        foreach ($keys as $key) {
            $value                 = Redis::get($key);
            $outputKey             = str_replace(self::PARENT_KEY, '', $key);
            $countries[$outputKey] = $value;
        }
        // Кладем в кэш, время жизни выставляется в общем конфиге (тип хранилища тоже можно выбрать в конфиге (Memcached, Redis, File))
        Cache::put(self::STATS_KEY, $countries, getenv('CACHE_LIFE_TIME'));

        return $countries;
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    public function update(array $data = [])
    {
        $key = sprintf(self::KEY_MASK, $data['country']);
        return (bool) Redis::incr($key);
    }
}