<?php

namespace App\Services\Providers;

use App\Services\ApiService;

class MeloloService
{
    public function __construct(protected ApiService $api) {}

    public function forYou(int $offset = 20): array
    {
        return $this->api->get('/melolo/foryou', ['offset' => $offset]);
    }

    public function latest(): array
    {
        return $this->api->get('/melolo/latest');
    }

    public function trending(): array
    {
        return $this->api->get('/melolo/trending');
    }

    public function search(string $query, int $limit = 10, int $offset = 0): array
    {
        return $this->api->get('/melolo/search', ['query' => $query, 'limit' => $limit, 'offset' => $offset], 120);
    }

    public function detail(string $bookId): array
    {
        return $this->api->get('/melolo/detail', ['bookId' => $bookId]);
    }

    public function stream(string $videoId): array
    {
        return $this->api->getNoCache('/melolo/stream', ['videoId' => $videoId]);
    }
}
