<?php

namespace App\Services\Providers;

use App\Services\ApiService;

class DramaBoxService
{
    public function __construct(protected ApiService $api) {}

    public function forYou(int $page = 1): array
    {
        return $this->api->get('/dramabox/foryou', ['page' => $page]);
    }

    public function latest(): array
    {
        return $this->api->get('/dramabox/latest');
    }

    public function trending(): array
    {
        return $this->api->get('/dramabox/trending');
    }

    public function search(string $query): array
    {
        return $this->api->get('/dramabox/search', ['query' => $query], 120);
    }

    public function detail(string $bookId): array
    {
        return $this->api->get('/dramabox/detail', ['bookId' => $bookId]);
    }

    public function allEpisodes(string $bookId): array
    {
        return $this->api->get('/dramabox/allepisode', ['bookId' => $bookId], 1800);
    }

    public function decrypt(string $url): array
    {
        return $this->api->getNoCache('/dramabox/decrypt', ['url' => $url]);
    }

    public function dubIndo(string $classify = 'terpopuler', int $page = 1): array
    {
        return $this->api->get('/dramabox/dubindo', ['classify' => $classify, 'page' => $page]);
    }
}
