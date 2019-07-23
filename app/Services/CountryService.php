<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Queue;

use App\Jobs\CacheCountries;


class CountryService
{
    const STATS_PRIMARY_KEY   = 'stats.countries.primary';
    const STATS_SECONDARY_KEY = 'stats.countries.secondary';
    const PARENT_KEY          = 'countries:';
    const KEY_MASK            = self::PARENT_KEY . '%s';

    /**
     * @return array
     */
    public function list()
    {
        // Если есть в первичном кэше - забираем стату оттуда, регулируем время жизни с помощью TTL в конфиге (как бы слейв)
        if ($stats = Cache::get(self::STATS_PRIMARY_KEY)) {
            return $stats;
        }

        // Подключаем сервис очередей, для фоновой обработки
        $cacheService = new CacheCountries();

        // Если есть в запасном кэше - забираем стату из него => отвечаем асинхронно => пишем в оба кэша фоном с помощью очередей,
        if ($stats = Cache::get(self::STATS_SECONDARY_KEY)) {
            Queue::push($cacheService);
        // Иначе делаем запись в кэши в риалтайме (истекло время ожидания запасного кэша, чего быть не должно при правильной настройке)
        } else {
            $cacheService->handle();
        }

        return $stats;
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
