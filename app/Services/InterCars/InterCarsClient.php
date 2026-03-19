<?php

namespace App\Services\InterCars;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class InterCarsClient
{
    private const TOKEN_CACHE_KEY = 'services.intercars.access_token';

    /**
     * @return bool
     */
    public function configured(): bool
    {
        return filled(config('services.intercars.client_id'))
            && filled(config('services.intercars.client_secret'))
            && filled(config('services.intercars.base_url'))
            && filled(config('services.intercars.token_url'));
    }


    /**
     * @param string $sku
     *
     * @return array|null
     */
    public function getCatalogProductBySku(string $sku): ?array
    {
        return $this->getCatalogProduct('sku', strtoupper($sku));
    }


    /**
     * @param string $index
     *
     * @return array|null
     */
    public function getCatalogProductByIndex(string $index): ?array
    {
        return $this->getCatalogProduct('index', trim($index));
    }


    /**
     * @param array $lines
     *
     * @return array
     */
    public function quote(array $lines): array
    {
        return $this->quoteBySku($lines);
    }


    /**
     * @param array $lines
     *
     * @return array
     */
    public function quoteBySku(array $lines): array
    {
        return $this->quoteBy('sku', $lines);
    }


    /**
     * @param array $lines
     *
     * @return array
     */
    public function quoteByIndex(array $lines): array
    {
        return $this->quoteBy('index', $lines);
    }


    /**
     * @param string $field
     * @param string $value
     *
     * @return array|null
     */
    private function getCatalogProduct(string $field, string $value): ?array
    {
        $response = $this->sendAuthenticatedGet('/ic/catalog/products', [
            $field => $value,
        ]);

        $products = $response->json('products', []);

        foreach ($products as $product) {
            if ((string) data_get($product, $field) === $value) {
                return $product;
            }
        }

        return $products[0] ?? null;
    }


    /**
     * @param string $field
     * @param array  $lines
     *
     * @return array
     */
    private function quoteBy(string $field, array $lines): array
    {
        if (empty($lines)) {
            return [];
        }

        $response = $this->sendAuthenticatedPost('/ic/inventory/quote', [
            'lines' => array_map(function ($line) use ($field) {
                return [
                    $field    => (string) ($line[$field] ?? ''),
                    'quantity' => (int) ($line['quantity'] ?? 1),
                ];
            }, $lines),
        ]);

        $data = $response->json();

        return is_array($data) ? $data : [];
    }


    /**
     * @param string $uri
     * @param array  $query
     *
     * @return Response
     */
    private function sendAuthenticatedGet(string $uri, array $query = []): Response
    {
        return $this->sendAuthenticatedRequest('get', $uri, [], $query);
    }


    /**
     * @param string $uri
     * @param array  $payload
     *
     * @return Response
     */
    private function sendAuthenticatedPost(string $uri, array $payload = []): Response
    {
        return $this->sendAuthenticatedRequest('post', $uri, $payload);
    }


    /**
     * @param string $method
     * @param string $uri
     * @param array  $payload
     * @param array  $query
     *
     * @return Response
     */
    private function sendAuthenticatedRequest(string $method, string $uri, array $payload = [], array $query = []): Response
    {
        $response = $this->dispatchAuthenticatedRequest($method, $uri, $payload, $query, $this->getAccessToken());

        if ($response->status() === 401) {
            Cache::forget(self::TOKEN_CACHE_KEY);

            $response = $this->dispatchAuthenticatedRequest($method, $uri, $payload, $query, $this->getAccessToken());
        }

        if ($response->failed()) {
            throw new RuntimeException($this->resolveErrorMessage($response));
        }

        return $response;
    }


    /**
     * @param string $method
     * @param string $uri
     * @param array  $payload
     * @param array  $query
     * @param string $token
     *
     * @return Response
     */
    private function dispatchAuthenticatedRequest(string $method, string $uri, array $payload, array $query, string $token): Response
    {
        $request = $this->businessRequest($token)->withHeaders([
            'Accept-Language' => config('services.intercars.language', 'hr'),
        ]);

        if ($method === 'get') {
            return $request->get($uri, $query);
        }

        return $request->post($uri, $payload);
    }


    /**
     * @return string
     */
    private function getAccessToken(): string
    {
        if ( ! $this->configured()) {
            throw new RuntimeException('Inter Cars API nije konfiguriran. Dodajte INTERCARS_CLIENT_ID i INTERCARS_CLIENT_SECRET u .env.');
        }

        $cached = Cache::get(self::TOKEN_CACHE_KEY);

        if ($cached) {
            return $cached;
        }

        $response = Http::acceptJson()
                        ->asForm()
                        ->timeout((int) config('services.intercars.timeout', 30))
                        ->withHeaders([
                            'Authorization' => 'Basic ' . base64_encode(config('services.intercars.client_id') . ':' . config('services.intercars.client_secret')),
                        ])
                        ->post(config('services.intercars.token_url'), [
                            'grant_type' => 'client_credentials',
                            'scope'      => config('services.intercars.scope', 'allinone'),
                        ]);

        if ($response->failed()) {
            throw new RuntimeException($this->resolveErrorMessage($response));
        }

        $token = (string) $response->json('access_token');

        if ($token === '') {
            throw new RuntimeException('Inter Cars API nije vratio access token.');
        }

        $expiresIn = max(60, (int) $response->json('expires_in', 3600) - 60);

        Cache::put(self::TOKEN_CACHE_KEY, $token, now()->addSeconds($expiresIn));

        return $token;
    }


    /**
     * @param string $token
     *
     * @return PendingRequest
     */
    private function businessRequest(string $token): PendingRequest
    {
        return Http::baseUrl(rtrim(config('services.intercars.base_url'), '/'))
                   ->acceptJson()
                   ->timeout((int) config('services.intercars.timeout', 30))
                   ->withToken($token);
    }


    /**
     * @param Response $response
     *
     * @return string
     */
    private function resolveErrorMessage(Response $response): string
    {
        $json = $response->json();
        $message = is_array($json)
            ? ($json['message'] ?? $json['error_description'] ?? $json['error'] ?? $json['title'] ?? null)
            : null;

        if ( ! $message) {
            $message = trim((string) $response->body());
        }

        if ( ! $message) {
            $message = 'Nepoznata greška pri komunikaciji s Inter Cars API-jem.';
        }

        return 'Inter Cars API greška [' . $response->status() . ']: ' . $message;
    }
}
