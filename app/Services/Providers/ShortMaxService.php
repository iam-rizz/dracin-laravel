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

    /**
     * Get a single episode with video URL.
     * API: /shortmax/episode?shortPlayId=X&episodeNumber=Y
     * Returns: {status, shortPlayId, shortPlayName, totalEpisodes,
     *           episode: {episodeNum, id, duration, locked, videoUrl: {video_480, video_720, video_1080}, cover}}
     */
    public function episode(string $shortPlayId, int $episodeNumber): array
    {
        return $this->api->getNoCache('/shortmax/episode', [
            'shortPlayId'  => $shortPlayId,
            'episodeNumber' => $episodeNumber,
        ]);
    }
}
