<?php

namespace App\Http\Controllers;

use App\Http\Requests\CountryRequest;
use App\Rules\CountryInAllowedList;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;

class CountryController extends Controller
{
    const PARENT_KEY = 'countries:';
    const KEY_MASK   = self::PARENT_KEY . '%s';

    protected $storage;

    public function __construct(Redis $storage)
    {
        $this->storage = $storage;
    }

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        // Если есть в кэше забираем стату оттуда, региулируем время жизни с помощью TTL в конфиге (как бы слейв)
        if ($stats = Cache::get('companies.stats')) {
            return response()->json($stats, 200);
        }
        // Если есть в кэше нет собираем по ключам их хранилища
        $keys      = Redis::keys(self::PARENT_KEY . '*');
        $countries = [];
        foreach ($keys as $key) {
            $value                 = Redis::get($key);
            $outputKey             = str_replace(self::PARENT_KEY, '', $key);
            $countries[$outputKey] = $value;
        }
        // Кладем в кэш время жизни выставляется в общем конфиге
        Cache::put('companies.stats', $countries, getenv('CACHE_LIFE_TIME'));

        return response()->json($countries, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\CountryRequest $request
     * @return \Illuminate\Http\Response
     */
    public function update(CountryRequest $request)
    {
        $data = $request->validated();
        // Custom Validations
        $this->validate($request, ['country' => new CountryInAllowedList()]);

        $key = sprintf(self::KEY_MASK, $data['country']);

        return response()->json(['success' => (bool) Redis::incr($key)], 200);
    }
}
