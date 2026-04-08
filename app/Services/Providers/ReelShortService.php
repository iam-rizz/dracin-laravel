<?php

namespace App\Services\Providers;

use App\Services\ApiService;

class ReelShortService
{
    public function __construct(protected ApiService $api) {}

    public function forYou(int $page = 1): array
    {
        return $this->api->get('/reelshort/foryou', ['page' => $page]);
    }

    public function homepage(): array
    {
        return $this->api->get('/reelshort/homepage');
    }

    public function search(string $query, int $page = 1): array
    {
        return $this->api->get('/reelshort/search', ['query' => $query, 'page' => $page], 120);
    }

    public function detail(string $bookId): array
    {
        return $this->api->get('/reelshort/detail', ['bookId' => $bookId]);
    }

    public function episode(string $bookId, int $episodeNumber): array
    {
        return $this->api->getNoCache('/reelshort/episode', ['bookId' => $bookId, 'episodeNumber' => $episodeNumber]);
    }
}
