<?php

namespace App\Jobs;

use App\Services\CountryService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;

class CacheCountries implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     *
     * @return array
     */
    public function handle()
    {
        // Собираем по ключам из хранилища
        $keys      = Redis::keys(CountryService::PARENT_KEY . '*');
        $countries = [];
        foreach ($keys as $key) {
            $value                 = Redis::get($key);
            $outputKey             = str_replace(CountryService::PARENT_KEY, '', $key);
            $countries[$outputKey] = $value;
        }
        // Кладем в кэш, время жизни выставляется в общем конфиге (тип хранилища тоже можно выбрать в конфиге (Memcached, Redis, File))
        Cache::put(CountryService::STATS_PRIMARY_KEY, $countries, getenv('CACHE_LIFE_TIME_PRIMARY'));
        // Дублируем данные в более долгоживущий кэш (можно бессмертный)
        Cache::put(CountryService::STATS_SECONDARY_KEY, $countries, getenv('CACHE_LIFE_TIME_SECONDARY'));

        return $countries;
    }
}
