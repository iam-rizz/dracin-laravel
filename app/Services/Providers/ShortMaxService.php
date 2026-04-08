<?php

namespace App\Services\Providers;

use App\Services\ApiService;

class ShortMaxService
{
    public function __construct(protected ApiService $api) {}

    public function forYou(int $page = 1): array
    {
        return $this->api->get('/shortmax/foryou', ['page' => $page]);
    }

    public function latest(): array
    {
        return $this->api->get('/shortmax/latest');
    }

    public function search(string $query): array
    {
        return $this->api->get('/shortmax/search', ['query' => $query], 120);
    }

    public function detail(string $shortPlayId): array
    {
        return $this->api->get('/shortmax/detail', ['shortPlayId' => $shortPlayId]);
    }

    public function allEpisodes(string $shortPlayId): array
    {
        return $this->api->get('/shortmax/allepisode', ['shortPlayId' => $shortPlayId], 1800);
    }
}
