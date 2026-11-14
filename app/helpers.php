<?php

if (! function_exists('area_route')) {
    /**
     * Generate a URL for a route inside the current user's area,
     * e.g. area_route('products.index') → route('admin.products.index') or route('staff.products.index').
     */
    function area_route(string $name, mixed $parameters = [], bool $absolute = true): string
    {
        return route(auth()->user()->area().'.'.$name, $parameters, $absolute);
    }
}
