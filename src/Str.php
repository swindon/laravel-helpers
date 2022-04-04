<?php

/* -------------------------------------------------------------------- */

/**
 * Randomly shuffles a string
 *
 * @param   string|null     $string
 * @return  string
 */
Str::macro( 'shuffle', function(?string $string) : string
{
    return $string ? str_shuffle($string) : null;
});

/* -------------------------------------------------------------------- */

/**
 * Reverse a string
 *
 * @param   string|null     $string
 * @return  string
 */
Str::macro( 'reverse', function(?string $string) : string
{
    return $string ? strrev($string) : null;
});

/* -------------------------------------------------------------------- */

/**
 * Calculate the similarity between two strings
 *
 * Requires package atomescrochus/laravel-string-similarities
 * @see https://github.com/atomescrochus/laravel-string-similarities
 *
 * @param   string|null     $a
 * @param   string|null     $b
 * @param   bool            $caseSensative
 * @param   bool            $smg
 * @return  float
 */
Str::macro( 'similarText', function(?string $a, ?string $b, bool $caseSensative = false, bool $smg = true) : float
{
    if( !$a || !$b ) return 0;
    $comparison = new \Atomescrochus\StringSimilarities\Compare();
    if( $caseSensative ) {
        return max([
            $comparison->similarText($a,$b)/100,
            $comparison->jaroWinkler($a,$b),
            $smg ? $comparison->smg($a,$b) : 0,
            $comparison->similarText($b,$a)/100,
            $comparison->jaroWinkler($b,$a),
            $smg ? $comparison->smg($b,$a) : 0,
        ]);
    } else {
        return max([
            $comparison->similarText($a,$b)/100,
            $comparison->jaroWinkler($a,$b),
            $smg ? $comparison->smg($a,$b) : 0,
            $comparison->similarText($b,$a)/100,
            $comparison->jaroWinkler($b,$a),
            $smg ? $comparison->smg($b,$a) : 0,
            $comparison->similarText(strtoupper($a),strtoupper($b))/100,
            $comparison->jaroWinkler(strtoupper($a),strtoupper($b)),
            $smg ? $comparison->smg(strtoupper($a),strtoupper($b)) : 0,
            $comparison->similarText(strtoupper($b),strtoupper($a))/100,
            $comparison->jaroWinkler(strtoupper($b),strtoupper($a)),
            $smg ? $comparison->smg(strtoupper($b),strtoupper($a)) : 0,
            $comparison->similarText(strtolower($a),strtolower($b))/100,
            $comparison->jaroWinkler(strtolower($a),strtolower($b)),
            $smg ? $comparison->smg(strtolower($a),strtolower($b)) : 0,
            $comparison->similarText(strtolower($b),strtolower($a))/100,
            $comparison->jaroWinkler(strtolower($b),strtolower($a)),
            $smg ? $comparison->smg(strtolower($b),strtolower($a)) : 0,
        ])*100;
    }
});

/* -------------------------------------------------------------------- */

/**
 * Generates a UUID (version 5)
 *
 * Requires package ramsey/uuid
 * @see https://github.com/ramsey/uuid
 *
 * @param   string|null     $name
 * @param   string|null     $namespace
 * @return  string
 */
Str::macro( 'uuid5', function(?string $name, ?string $namespace = null) : string
{
    return \Ramsey\Uuid\Uuid::uuid5( $namespace ?: \Ramsey\Uuid\Uuid::NAMESPACE_DNS, $name )->toString();
});

/* -------------------------------------------------------------------- */

/**
 * Extended version of Str::random()
 *
 * @param   int|null        $length
 * @param   string          $characters
 * @return  string
 */
Str::macro( 'randomExt', function(?int $length = 16, string $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789') : string
{
    if( $length <= 0 || !strlen($characters) ) return null;
    return substr( str_shuffle( implode( '', array_fill( 0, $length, $characters ) ) ), 0, $length );
});

/* -------------------------------------------------------------------- */

/**
 * Generate a random password/code
 *
 * @param   int|null        $length
 * @param   string          $characters
 * @param   bool            $removeAmbiguous
 * @return  string
 */
Str::macro( 'password', function(?int $length = 8, string $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!"$%&\'()*+,-./:;<=>?@[\]^_`{|}~', bool $removeAmbiguous = false) : string
{
    if( $removeAmbiguous ) {
        $characters = str_replace(str_split('B8G6I1l|0OQDS5$Z2()[]{}:;,.\'"`!$-~£¢§'), '', $characters);
    }
    if( $length <= 0 || !strlen($characters) ) return null;
    return substr( str_shuffle( implode( '', array_fill( 0, $length, $characters ) ) ), 0, $length );
});

/* -------------------------------------------------------------------- */

/**
 * XSS filtering, cleans various UTF encodings & nested exploits
 *
 * @param   string|null     $string
 * @return  string
 */
Str::macro( 'xss', function(?string $string) : string
{
    // Fix &entity\n;
    $string = str_replace(array('&amp;', '&lt;', '&gt;'), array('&amp;amp;', '&amp;lt;', '&amp;gt;'), $string);
    $string = preg_replace('/(&#*\w+)[\x00-\x20]+;/u', '$1;', $string);
    $string = preg_replace('/(&#x*[0-9A-F]+);*/iu', '$1;', $string);
    $string = html_entity_decode($string, ENT_COMPAT, 'UTF-8');

    // Remove any attribute starting with "on" or xmlns
    $string = preg_replace('#(<[^>]+?[\x00-\x20"\'])(?:on|xmlns)[^>]*+>#iu', '$1>', $string);

    // Remove javascript: and vbscript: protocols
    $string = preg_replace('#([a-z]*)[\x00-\x20]*=[\x00-\x20]*([`\'"]*)[\x00-\x20]*j[\x00-\x20]*a[\x00-\x20]*v[\x00-\x20]*a[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2nojavascript...', $string);
    $string = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*v[\x00-\x20]*b[\x00-\x20]*s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:#iu', '$1=$2novbscript...', $string);
    $string = preg_replace('#([a-z]*)[\x00-\x20]*=([\'"]*)[\x00-\x20]*-moz-binding[\x00-\x20]*:#u', '$1=$2nomozbinding...', $string);

    // Only works in IE: <span style="width: expression(alert('Ping!'));"></span>
    $string = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?expression[\x00-\x20]*\([^>]*+>#i', '$1>', $string);
    $string = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?behaviour[\x00-\x20]*\([^>]*+>#i', '$1>', $string);
    $string = preg_replace('#(<[^>]+?)style[\x00-\x20]*=[\x00-\x20]*[`\'"]*.*?s[\x00-\x20]*c[\x00-\x20]*r[\x00-\x20]*i[\x00-\x20]*p[\x00-\x20]*t[\x00-\x20]*:*[^>]*+>#iu', '$1>', $string);

    // Remove namespaced elements (we do not need them)
    $string = preg_replace('#</*\w+:\w[^>]*+>#i', '', $string);

    // Remove really unwanted tags
    $old_string = null;
    while ($old_string !== $string) {
        $old_string = $string;
        $string = preg_replace('#</*(?:applet|b(?:ase|gsound|link)|embed|frame(?:set)?|i(?:frame|layer)|l(?:ayer|ink)|meta|object|s(?:cript|tyle)|title|xml)[^>]*+>#i', '', $string);
    }

    // we are done...
    return $string;
}

