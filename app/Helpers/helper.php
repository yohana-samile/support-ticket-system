<?php
    use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

    if (!function_exists('str_unique')) {
        /**
         * @param int $length
         * @return string
         */
        function str_unique(int $length = 30): string
        {
            $side = rand(0,1);
            $salt = rand(0,9);
            $len = $length - 1;
            $string = \Illuminate\Support\Str::random($len <= 0 ? 7 : $len);
            $separatorPos = (int) ceil($length/4);
            $string = $side === 0 ? ($salt . $string) : ($string . $salt);
            $string = substr_replace($string, '-', $separatorPos, 0);
            return substr_replace($string, '-', -$separatorPos, 0);
        }
    }


if (!function_exists('negative_value')) {
    /**
     * @param int|float $value
     * @param $float
     * @return int|float
     */
    function negative_value(int|float $value, $float = false): int|float
    {
        if ($float) {
            $value = (float) $value;
        }
        return 0 - abs($value);
    }
}

if (! function_exists('nextbyte_theme_config')) {
    /**
     * Returns a config value from the current theme's config file.
     * It assumes the theme's config namespace is the same as the view namespace.
     *
     * @param string
     * @return string
     */
    function nextbyte_theme_config($key)
    {
        $namespacedKey = config('nextbyte.ui.show_developer_link');
        $namespacedKey = config('nextbyte.ui.developer_link');

        // if the config exists in the theme config file, use it
        if (config()->has($namespacedKey)) {
            return config($namespacedKey);
        }

        if (config()->has($namespacedKey)) {
            return config($namespacedKey);
        }

        Log::error('Could not find config key: '.$key.'. Neither in the nextbyte theme, nor in the fallback theme, nor in ui.');

        return null;
    }
}



if (!function_exists('toHtmlString')) {

    /**
     * Return the public url of the application
     *
     * @return type string
     */
    function toHtmlString($html) {
        return new \Illuminate\Support\HtmlString($html);
    }

}

if (!function_exists('html_attributes_implode')) {

    /**
     * Return the public url of the application
     *
     * @return type string
     */
    function html_attributes_implode($attributes) {

        if(count($attributes)) {
            return collect($attributes)->map(function ($value, $key) {
                return is_bool($value) ? ($value ? $key : '') : "{$key}=\"" . e($value) . "\"";
            })->implode(' ');
        }

        return null;
    }

}

if (! function_exists('link_to_route')) {
    /**
     * Generate a HTML link to a named route.
     *
     * @param string $name
     * @param string $title
     * @param array  $parameters
     * @param array  $attributes
     *
     * @return \Illuminate\Support\HtmlString
     */
    function link_to_route($name, $title = null, $parameters = [], $attributes = [])
    {
        // Generate the URL using the named route and parameters
        $url = route($name, $parameters);

        // Translate the title if necessary
        $translatedTitle = __($title);

        // Prepare the attributes as a string
        $attributesString = collect($attributes)
            ->map(function ($value, $key) {
                return is_bool($value) ? ($value ? $key : null) : "{$key}=\"{$value}\"";
            })
            ->filter()
            ->implode(' ');

        // Return the complete anchor tag
        return toHtmlString("<a href=\"{$url}\" {$attributesString}>{$translatedTitle}</a>");
    }
}

if (! function_exists('includeRouteFiles')) {
    /**
     * Loops through a folder and requires all PHP files
     * Searches sub-directories as well.
     *
     * @param $folder
     */
    function includeRouteFiles($folder)
    {
        try {
            $rdi = new recursiveDirectoryIterator($folder);
            $it = new recursiveIteratorIterator($rdi);

            while ($it->valid()) {
                if (! $it->isDot() && $it->isFile() && $it->isReadable() && $it->current()->getExtension() === 'php') {
                    require $it->key();
                }

                $it->next();
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}


if (!function_exists('getFallbackLocale')) {

    /**
     * Get the fallback locale
     *
     * @return \Illuminate\Foundation\Application|mixed
     */
    function getFallbackLocale() {
        return config('app.fallback_locale');
    }

}

if (!function_exists('getLanguageBlock')) {

    /**
     * Get the language block with a fallback
     *
     * @param $view
     * @param array $data
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    function getLanguageBlock($view, $data = []) {
        $components = explode("lang", $view);
        $current = $components[0] . "lang." . app()->getLocale() . "." . $components[1];
        $fallback = $components[0] . "lang." . getFallbackLocale() . "." . $components[1];

        if (view()->exists($current)) {
            return view($current, $data);
        } else {
            return view($fallback, $data);
        }
    }

}


if (! function_exists('access')) {
    /**
     * Access (lol) the Access:: facade as a simple function.
     */
    function access()
    {
        return app('access');
    }
}

function user_id(){
    return optional(Auth::user())->id;
}

function userFullName(){
    return Auth::user()->name;
}

function user(){
    return Auth::user();
}

function isAdmin(){
    return Auth::user()->is_super_admin;
}

function initials() {
    $fullName = userFullName();
    $nameParts = explode(' ', trim($fullName));

    return count($nameParts) > 2
        ? strtoupper(substr($nameParts[0], 0, 1) . substr(end($nameParts), 0, 1))
        : strtoupper(substr($nameParts[0], 0, 1) . (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : ''));
}

if (! function_exists('getTodayDate')) {

    function getTodayDate()
    {
        return \Carbon\Carbon::now()->format('Y-n-j');

    }
}

if (!function_exists('formatBytes')) {
    function formatBytes($bytes, $precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        return round($bytes / pow(1024, $pow), $precision) . ' ' . $units[$pow];
    }
}

if (! function_exists("remove_all_white_spaces")) {
    function remove_all_white_spaces($value) {
        $value =  preg_replace('/\s+/', '', $value );
        return $value;
    }
}


if (! function_exists("remove_extra_white_spaces")) {
    function remove_extra_white_spaces($value) {
        $value =  preg_replace('/\s+/', ' ', $value );
        $value = remove_first_this_char($value,' ');
        $value = remove_last_this_char($value,' ');
        return $value;
    }
}

if (! function_exists("remove_all_special_chars")) {
    function remove_all_special_chars($value) {
        $value = str_replace(' ', '-', $value); // Replaces all spaces with hyphens.
        $value =  preg_replace('/[^A-Za-z0-9\-]/', '', $value); // Removes special chars.
        $value = str_replace('-', ' ', $value);
        return $value;
    }
}

if (! function_exists("remove_filter_url")) {
    function remove_filter_url($filterName): string
    {
        $query = request()->query();
        unset($query[$filterName]);
        return url()->current() . (count($query) ? '?' . http_build_query($query) : '');
    }
}

if (!function_exists('getStatusBadge')) {
    function getStatusBadge(bool $isActive): string
    {
        if ($isActive) {
            return '<span class="badge bg-primary text-white">'.__('label.active').'</span>';
        }
        return '<span class="badge bg-danger text-white">'.__('label.inactive').'</span>';
    }
}

if (!function_exists('getManagerBadge')) {
    function getManagerBadge(bool $isManager): string
    {
        if ($isManager) {
            return '<span class="badge bg-primary text-white">'.__('label.yes').'</span>';
        }
        return '<span class="badge bg-danger text-white">'.__('label.no').'</span>';
    }
}


if (!function_exists('getStatusBadgeColor')) {
    function getStatusBadgeColor($status)
    {
        switch (strtolower($status)) {
            case 'open':
                return 'primary';
            case 'resolved':
                return 'success';
            case 'closed':
                return 'secondary';
            case 'escalated':
                return 'danger';
            case 'reopened':
                return 'info';
            default:
                return 'dark';
        }
    }
}

if (!function_exists('getPriorityBadgeColor')) {
    function getPriorityBadgeColor($priority)
    {
        switch (strtolower($priority)) {
            case 'low':
                return 'success';
            case 'medium':
                return 'warning';
            case 'high':
                return 'danger';
            case 'critical':
                return 'dark';
            default:
                return 'info';
        }
    }
}
