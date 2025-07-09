<?php

namespace App\Providers;

use App\Services\Access\Access;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use Spatie\Html\Html;
use Illuminate\Support\Facades\URL;


/**
 * Class AccessServiceProvider.
 */
class CustomMacroProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;
    protected $model;

    /**
     * Package boot method.
     */
    public function boot()
    {
        $this->registerCollectiveHtml();
        $this->registerCollectiveFormHtmlInput();
        $this->registerCollectiveHtmlFormOpenClose();
        $this->registerCollectiveLinkToRoute();
    }

public function registerCollectiveHtml()
{
    // Register the script macro
    Html::macro('script', function ($src) {
        return Html::element('script')
            ->attribute('src', $src);
    });

    Html::macro('style', function ($href) {
        return Html::element('link')
            ->attribute('rel', 'stylesheet')
            ->attribute('href', $href);
    });
}


public function registerCollectiveFormHtmlInput()
{


    Html::macro('labels', function ($name, $value = null, $attributes = []) {

        return toHtmlString('<label for="'.$name.'" ' . html_attributes_implode($attributes) . '>'. e($value). '</label>');
    });
    // Text Input
    Html::macro("texts", function ($name, $value = null, $attributes = [])  {
        return toHtmlString('<input type="text" name="' . $name . '" value="' . ($value ?? old($name) ?? $this->model->$name ?? null)  . '" ' . html_attributes_implode($attributes) . '>');
    });

    // Email Input
    Html::macro('emails', function ($name, $value = null, $attributes = []) {
        return toHtmlString('<input type="email" name="' . $name . '" value="' .  ($value ?? old($name) ?? $this->model->$name ?? null)  . '" ' . html_attributes_implode($attributes)  . '>');
    });

    // Password Input
    Html::macro('passwords', function ($name, $attributes = []) {
        return toHtmlString('<input type="password" name="' . $name . '" ' .html_attributes_implode($attributes) . '>');
    });

    // Textarea
    Html::macro('textareas', function ($name, $value = null, $attributes = []) {
        return toHtmlString('<textarea name="' . $name . '" ' .html_attributes_implode($attributes) . '>' .  ($value ?? old($name) ?? $this->model->$name ?? null) . '</textarea>');
    });

    // Checkbox
    Html::macro('checkboxes', function ($name, $value = 1, $checked = false, $attributes = []) {
        $checkedAttribute = $checked ? 'checked' : '';
        return  toHtmlString('<input type="checkbox" name="' . $name . '" value="' .  ($value ?? old($name) ?? $this->model->$name ?? null) . '" ' . $checkedAttribute . ' ' .html_attributes_implode($attributes) . '>');
    });

    // Radio Button
    Html::macro('radios', function ($name, $value = null, $checked = false, $attributes = []) {
        $checkedAttribute = $checked ? 'checked' : '';
        return toHtmlString('<input type="radio" name="' . $name . '" value="' .  ($value ?? old($name) ?? $this->model->$name) . '" ' . $checkedAttribute . ' ' .html_attributes_implode($attributes) . '>');
    });

    // Select
    Html::macro('selects', function ($name, $options = [], $selected = null, $attributes = []) {
//        $optionsHtml = '';
        if(isset($attributes['placeholder'])){
            $optionsHtml = '<option name="' . $name . '" value="' . null. '" ' . '>' .($attributes['placeholder'] ?? ''). '</option>';
        }
        foreach ($options as $value => $display) {
            $selectedAttribute = (($value) == ($selected ?? $this->model->$name ?? null)) ? 'selected' : '';
            $optionsHtml .= '<option name="' . $name . '" value="' . ($value). '" ' . $selectedAttribute . '>' . e($display) . '</option>';
        }
        return toHtmlString('<select name="' . $name . '" ' .html_attributes_implode($attributes) . '>' . $optionsHtml . '</select>');
    });

    // Submit Button
    Html::macro('submits', function ($value = 'Submit', $attributes = []) {
        return toHtmlString('<button type="submit" ' .html_attributes_implode($attributes) . '>' . e($value) . '</button>');
    });

    // Hidden Input
    Html::macro('hiddens', function ($name, $value = null, $attributes = []) {
        return toHtmlString('<input type="hidden" name="' . $name . '" value="' .($value ?? old($name) ?? $this->model->$name ?? null) . '" ' .html_attributes_implode($attributes) . '>');
    });
}

    public function registerCollectiveHtmlFormOpenClose()
    {

        Html::macro('formOpen', function ($options = []) {
            // Set method and action based on options
            $method = strtoupper($options['method'] ?? 'POST');
            $action = '';

            if (isset($options['route'])) {
                $action = route($options['route']);
            } elseif (isset($options['url'])) {
                $action = $options['url'];
            } else {
                $action = URL::current();
            }

            // Check if the form is for file uploads
            $enctype = isset($options['enctype']) ? "enctype=\"{$options['enctype']}\"" : '';

            // Handle method spoofing for PUT, PATCH, DELETE
            $methodField = in_array($method, ['GET', 'POST']) ? '' : method_field($method);

            // CSRF field for non-GET requests
            $csrfField = $method !== 'GET' ? csrf_field() : '';

            // Prepare additional attributes
            $attributes = collect($options)
                ->except(['method', 'route', 'url', 'enctype'])
                ->map(function ($value, $key) {
                    return is_bool($value) ? ($value ? $key : null) : "{$key}=\"{$value}\"";
                })
                ->filter()
                ->implode(' ');

            // Build the opening form tag
            return toHtmlString("<form action=\"{$action}\" method=\"".strtolower($method)."\" {$enctype} {$attributes}>{$methodField}{$csrfField}");
        });


        /*Form Model*/
        Html::macro('formModel', function ($model, $options = []) {
            // Set default method and action based on options
            $method = strtoupper($options['method'] ?? 'POST');
            $method_formatted = ($method == 'PUT') ? 'POST' : $method;
            $action = '';

            if (isset($options['route'])) {
                // Generate the route with model attributes
                $action = route($options['route'][0], $options['route'][1] ?? []);
            } elseif (isset($options['url'])) {
                $action = $options['url'];
            } else {
                $action = url()->current();
            }

            // Check if the form is for file uploads
            $enctype = isset($options['enctype']) ? "enctype=\"{$options['enctype']}\"" : '';

            // Handle method spoofing for PUT, PATCH, DELETE
            $methodField = in_array($method, ['GET', 'POST']) ? '' : method_field($method);

            // CSRF field for non-GET requests
            $csrfField = $method !== 'GET' ? csrf_field() : '';

            // Prepare additional attributes
            $attributes = collect($options)
                ->except(['method', 'route', 'url', 'enctype'])
                ->map(function ($value, $key) {
                    return is_bool($value) ? ($value ? $key : null) : "{$key}=\"{$value}\"";
                })
                ->filter()
                ->implode(' ');
           $this->model = $model;
            // Create form opening tag
            $formOpen = "<form action=\"{$action}\" method=\"".strtolower($method_formatted)."\" {$enctype} {$attributes}>{$methodField}{$csrfField}";

            return toHtmlString($formOpen);
        });



/*form close*/
        Html::macro('formClose', function () {
            app()->forgetInstance('formModel');
            return toHtmlString('</form>');
        });
    }


    public function registerCollectiveLinkToRoute()
    {
        Html::macro('linkToRoute', function ($name, $title = null, $parameters = [], $attributes = []) {
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
            return "<a href=\"{$url}\" {$attributesString}>{$translatedTitle}</a>";
        });
    }
}
