<?php

namespace App\Repositories;

use App\Model\Country;

class PropertyRepository extends BaseRepository
{
    /** @var \App\Model\Country $model */
    protected $model;

    public function __construct()
    {
        parent::__construct();
    }

    public function makeModel()
    {
        return $this->setModel($this->model());
    }

    public function setModel($model)
    {
        $this->model = app()->make($model);

        return $this;
    }

    public function model()
    {
        return Country::class;
    }

    public function all($columns = ['*'])
    {
        return $this->model->all($columns);
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function push($key, $value)
    {
        parent::push($key, $value);
    }

    /**
     * @param string $key
     * @param mixed  $value
     */
    public function prepend($key, $value)
    {
        parent::prepend($key, $value);
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        return parent::has($key);
    }

    /**
     * @param array|string $key
     * @param null         $value
     */
    public function set($key, $value = null)
    {
        parent::set($key, $value);
    }
}