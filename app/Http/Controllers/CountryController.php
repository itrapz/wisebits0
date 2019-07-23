<?php

namespace App\Http\Controllers;

use App\Http\Requests\CountryRequest;
use App\Services\CountryService;
use App\Rules\CountryInAllowedList;

class CountryController extends Controller
{
    /** @var \App\Services\CountryService */
    public $service;

    public function __construct(CountryService $service)
    {
        $this->service = $service;
    }

    /**
     * Display a listing of the resource.
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $list = $this->service->list();
        return response()->json($list, 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\CountryRequest $request
     * @return \Illuminate\Http\Response
     */
    public function update(CountryRequest $request)
    {
        // Базовая валидация по длине и наличию
        $data = $request->validated();
        // Кастомная валидация, сверяем входит ли страна в список допустимых
        $this->validate($request, ['country' => new CountryInAllowedList()]);

        // Обновляем (инкрементим) запись по прошедшему валидацию ключу
        $success = $this->service->update($data);

        return response()->json(['success' => $success], 200);
    }
}
