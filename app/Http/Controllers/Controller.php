<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function welcome(): string
    {
        return "Welcome to Spurt X ".env("APP_ENV")." API Version 1";
    }

    /**
     * This returns a signed in User Id
     * @return mixed
     */
    public function getUserId()
    {
        return auth()->id();
    }

    public function getUser()
    {
        return auth()->user();
    }

    /**
     * Translates an array to pagination
     * @param array $collections
     * @param Request $request
     * @return LengthAwarePaginator
     */
    public function arrayPaginator(array $collections, Request $request): LengthAwarePaginator
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 5);
        $limit = !$limit? 5 : $limit;//if limit was not available or set to 0
        $offset = ($page * $limit) - $limit;

        return new LengthAwarePaginator(
            array_slice($collections, $offset, $limit, false),
            count($collections), $limit, $page,
            [ 'path' => $request->url(), 'query' => $request->query() ]
        );
    }
}
