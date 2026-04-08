<?php

namespace App\Services\Providers;

use App\Services\ApiService;

class NetShortService
{
    public function __construct(protected ApiService $api) {}

    public function forYou(int $page = 1): array
    {
        return $this->api->get('/netshort/foryou', ['page' => $page]);
    }

    public function theaters(): array
    {
        return $this->api->get('/netshort/theaters');
    }

    public function search(string $query): array
    {
        return $this->api->get('/netshort/search', ['query' => $query], 120);
    }

    public function allEpisodes(string $shortPlayId): array
    {
        return $this->api->get('/netshort/allepisode', ['shortPlayId' => $shortPlayId], 1800);
    }
}
