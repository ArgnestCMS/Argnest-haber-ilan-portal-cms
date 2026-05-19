<?php

namespace App\Http\Controllers;

use App\Support\SearchDiscoveryService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function index(Request $request, SearchDiscoveryService $search): View
    {
        $query = (string) $request->query('q', '');
        $type = $request->query('type');
        $data = $search->search($query, is_string($type) ? $type : null, 8);

        return view('frontend.search', $data);
    }

    public function instant(Request $request, SearchDiscoveryService $search): JsonResponse
    {
        $query = (string) $request->query('q', '');

        return response()->json($search->instant($query, 4));
    }
}
