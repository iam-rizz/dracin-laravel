<?php

namespace App\Services\Providers;

use App\Services\ApiService;

class FreeReelsService
{
    public function __construct(protected ApiService $api) {}

    public function forYou(int $offset = 0): array
    {
        return $this->api->get('/freereels/foryou', ['offset' => $offset]);
    }

    public function homepage(): array
    {
        return $this->api->get('/freereels/homepage');
    }

    public function search(string $query): array
    {
        return $this->api->get('/freereels/search', ['query' => $query], 120);
    }

    public function detailAndAllEpisode(string $key): array
    {
        return $this->api->get('/freereels/detailAndAllEpisode', ['key' => $key], 1800);
    }
}
