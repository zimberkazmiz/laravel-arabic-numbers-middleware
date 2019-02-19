<?php

namespace Yemenifree\LaravelArabicNumbersMiddleware\Middleware;

use Illuminate\Config\Repository;
use Illuminate\Foundation\Http\Middleware\TransformsRequest;

abstract class BaseNumbersMiddleware extends TransformsRequest
{
    /** @var array */
    protected $except = [];

    /** @var string */
    protected $from = 'eastern';

    /** @var array */
    protected $farsiNumbers = [
        '۰' => 0,
        '١' => 1,
        '٢' => 2,
        '٣' => 3,
        '۴' => 4,
        '۵' => 5,
        '۶' => 6,
        '٧' => 7,
        '٨' => 8,
        '٩' => 9,
    ];

    /** @var array */
    protected $arabicNumbers = [
        '٤' => 4,
        '٥' => 5,
        '٦' => 6,
    ];

    /** @var array|mixed */
    protected $config;

    /**
     * BaseNumbersMiddleware constructor.
     *
     * @param Repository $config
     */
    public function __construct(Repository $config)
    {
        $this->config = $config->get('arabic-numbers-middleware');
    }

    /**
     * get except fields.
     *
     * @return array
     */
    public function getExcept()
    {
        return $this->except + $this->getOption('except_from_all', []) + $this->attributes;
    }

    /**
     * get options from config.
     * @param string $key
     * @param null $default
     * @return array
     */
    protected function getOption($key, $default = null)
    {
        return array_get($this->config, $key, $default);
    }

    /**
     * Transform the given value.
     *
     * @param  string $key
     * @param  mixed $value
     * @return mixed
     */
    protected function transform($key, $value)
    {
        if (in_array($key, $this->getExcept(), true)) {
            return $value;
        }

        return is_string($value) ? $this->transformNumber($value) : $value;
    }

    /**
     * transform eastern/(arabic|english) numbers to (arabic|english)/eastern numbers inside string.
     *
     * @param string $value
     * @return string
     */
    protected function transformNumber($value)
    {
        return strtr($value, $this->getNumbers());
    }

    /**
     * get array numbers to transforms.
     *
     * @return array
     */
    protected function getNumbers()
    {
        return $this->isFromEastern() ? $this->getEasternNumbers() : $this->getWesternNumbers();
    }

    /**
     * check if transform from (arabic|english) to eastern.
     *
     * @return bool
     */
    public function isFromEastern()
    {
        return $this->from === 'eastern';
    }

    /**
     * Get eastern numbers array.
     *
     * @return array
     */
    public function getEasternNumbers()
    {
        return $this->farsiNumbers + $this->arabicNumbers;
    }

    public function getWesternNumbers()
    {
        return \array_flip($this->farsiNumbers);
    }
}
