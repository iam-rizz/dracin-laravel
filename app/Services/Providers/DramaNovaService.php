<?php

namespace App\Services\Providers;

use App\Services\ApiService;

class DramaNovaService
{
    public function __construct(protected ApiService $api) {}

    public function home(int $page = 1): array
    {
        return $this->api->get('/dramanova/home', ['page' => $page]);
    }

    public function search(string $query): array
    {
        return $this->api->get('/dramanova/search', ['query' => $query], 120);
    }

    public function detail(string $dramaId): array
    {
        return $this->api->get('/dramanova/detail', ['dramaId' => $dramaId]);
    }

    public function getVideo(string $fileId): array
    {
        return $this->api->getNoCache('/dramanova/getvideo', ['fileId' => $fileId]);
    }
}
