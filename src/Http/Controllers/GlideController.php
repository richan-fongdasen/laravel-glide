<?php

namespace RichanFongdasen\Glide\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller;
use RichanFongdasen\Glide\Facade\Glide;
use RichanFongdasen\Glide\GlideRequest;
use Symfony\Component\HttpFoundation\StreamedResponse;

class GlideController extends Controller
{
    use AuthorizesRequests;
    use DispatchesJobs;
    use ValidatesRequests;

    /**
     * Display the specified asset resource.
     *
     * @param GlideRequest $request
     * @return StreamedResponse
     */
    public function show(GlideRequest $request): StreamedResponse
    {
        return Glide::respondToRequest($request);
    }
}
