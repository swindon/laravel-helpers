<?php

/* -------------------------------------------------------------------- */

/**
 * Validate minimum age based on date
 *
 * @param   string              $attribute
 * @param   string|int|null     $value
 * @param   array               $parameters
 * @param   Validator           $validator
 * @return  bool
 */
Validator::extend('min_age', function (string $attribute, $value = null, array $parameters = [], Validator $validator) : bool
{
    try {
        return \Carbon\Carbon::now()->diff(new \Carbon\Carbon($value))->y >= $parameters[0];
    } catch (\Throwable $e) {
        return false;
    }
});

/**
 * Adds message replacement for min_age validation
 *
 * @param   string              $message
 * @param   string              $attribute
 * @param   string              $rule
 * @param   array               $parameters
 * @param   Validator           $validator
 * @return  string
 */
Validator::replacer('min_age', function (string $message = 'validation.min_age', string $attribute, string $rule, array $parameters = [], Validator $validator) : string
{
    if( $message == 'validation.min_age' || !$message ) {
        $message = 'Minimum age :min_age.';
    }
    try {
        \Carbon\Carbon::parse(request()->{$attribute});
    } catch (\Throwable $e) {
        return 'Incorrect date format.';
    }
    return str_replace(':min_age', $parameters[0], $message);
});

/* -------------------------------------------------------------------- */

/**
 * Validate maximum age based on date
 *
 * @param   string              $attribute
 * @param   string|int|null     $value
 * @param   array               $parameters
 * @param   Validator           $validator
 * @return  bool
 */
Validator::extend('max_age', function (string $attribute, $value = null, array $parameters = [], Validator $validator) : bool
{
    try {
        return \Carbon\Carbon::now()->diff(new \Carbon\Carbon($value))->y <= $parameters[0];
    } catch (\Throwable $e) {
        return false;
    }
});

/**
 * Adds message replacement for max_age validation
 *
 * @param   string              $message
 * @param   string              $attribute
 * @param   string              $rule
 * @param   array               $parameters
 * @param   Validator           $validator
 * @return  string
 */
Validator::replacer('max_age', function (string $message = 'validation.max_age', string $attribute, string $rule, array $parameters = [], Validator $validator) : string
{
    if( $message == 'validation.max_age' || !$message ) {
        $message = 'Maximum age :max_age.';
    }
    try {
        \Carbon\Carbon::parse(request()->{$attribute});
    } catch (\Throwable $e) {
        return 'Incorrect date format.';
    }
    return str_replace(':max_age', $parameters[0], $message);
});

/* -------------------------------------------------------------------- */

/**
 * Validates uuid format
 *
 * Requires package ramsey/uuid
 * @see https://github.com/ramsey/uuid
 *
 * @param   string              $attribute
 * @param   string|int|null     $value
 * @param   array               $parameters
 * @param   Validator           $validator
 * @return  bool
 */
Validator::extend('uuid', function (string $attribute, $value = null, array $parameters = [], Validator $validator) : bool
{
    return \Ramsey\Uuid\Uuid::isValid($value);
});

/**
 * Adds message replacement for uuid validation
 *
 * @param   string              $message
 * @param   string              $attribute
 * @param   string              $rule
 * @param   array               $parameters
 * @param   Validator           $validator
 * @return  string
 */
Validator::replacer('uuid', function (string $message = 'validation.uuid', string $attribute, string $rule, array $parameters = [], Validator $validator) : string
{
    if( $message == 'validation.uuid' || !$message ) {
        $message = 'Uuid :uuid is invalid.';
    }
    return str_replace(':uuid', request()->{$attribute}, $message);
});

