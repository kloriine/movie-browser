<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class OmdbService
{
    protected $client;
    protected $apiKey;
    protected $baseUrl = 'http://www.omdbapi.com/';

    public function __construct()
    {
        $this->client = new Client([
            'timeout' => 10,
            'verify' => false,
        ]);
        $this->apiKey = config('services.omdb.api_key');
    }

    /**
     * Search movies by title
     *
     * @param string $search
     * @param int $page
     * @param string|null $type
     * @param string|null $year
     * @return array
     */
    public function searchMovies($search, $page = 1, $type = null, $year = null)
    {
        try {
            $params = [
                'apikey' => $this->apiKey,
                's' => $search,
                'page' => $page,
            ];

            if ($type && $type !== 'all') {
                $params['type'] = $type;
            }

            if ($year) {
                $params['y'] = $year;
            }

            $cacheKey = 'omdb_search_' . md5(json_encode($params));
            
            return Cache::remember($cacheKey, 3600, function () use ($params) {
                $response = $this->client->get($this->baseUrl, ['query' => $params]);
                return json_decode($response->getBody()->getContents(), true);
            });
        } catch (\Exception $e) {
            Log::error('OMDB API Error: ' . $e->getMessage());
            return ['Response' => 'False', 'Error' => 'API request failed'];
        }
    }

    /**
     * Get movie detail by IMDB ID
     *
     * @param string $imdbId
     * @return array
     */
    public function getMovieDetail($imdbId)
    {
        try {
            $cacheKey = 'omdb_detail_' . $imdbId;
            
            return Cache::remember($cacheKey, 3600, function () use ($imdbId) {
                $response = $this->client->get($this->baseUrl, [
                    'query' => [
                        'apikey' => $this->apiKey,
                        'i' => $imdbId,
                        'plot' => 'full'
                    ]
                ]);
                return json_decode($response->getBody()->getContents(), true);
            });
        } catch (\Exception $e) {
            Log::error('OMDB API Error: ' . $e->getMessage());
            return ['Response' => 'False', 'Error' => 'API request failed'];
        }
    }
}