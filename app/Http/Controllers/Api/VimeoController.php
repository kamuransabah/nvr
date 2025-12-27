<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use Vimeo\Laravel\Facades\Vimeo;

class VimeoController extends Controller
{
    public function search(Request $req)
    {
        $q    = trim((string) $req->get('q', ''));
        $page = max(1, (int) $req->get('page', 1));
        $per  = 30;

        $key = "vimeo_search:" . md5($q . '|' . $page . '|' . $per);

        $data = Cache::remember($key, now()->addMinutes(5), function () use ($q, $page, $per) {
            $params = [
                'per_page'  => $per,
                'page'      => $page,
                'sort'      => 'date',
                'direction' => 'desc',
                'fields'    => 'uri,name,pictures.sizes,duration',
            ];
            if ($q !== '') $params['query'] = $q;

            $res = Vimeo::request('/me/videos', $params, 'GET');

            $items = collect($res['body']['data'] ?? [])->map(function ($v) {
                $id = (int) last(explode('/', $v['uri'])); // /videos/123 -> 123
                return [
                    'id'    => (string) $id,
                    'text'  => $v['name'] ?? ('Video #' . $id),
                    'thumb' => $v['pictures']['sizes'][1]['link'] ?? null,
                    'duration' => $v['duration'] ?? 0,
                ];
            })->values();

            $hasMore = !empty($res['body']['paging']['next']);

            return ['results' => $items, 'pagination' => ['more' => $hasMore]];
        });

        return response()->json($data);
    }
}
