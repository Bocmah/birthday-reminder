<?php

declare(strict_types=1);

namespace BirthdayReminder\Infrastructure\Http\Middleware;

use Http\Promise\Promise;
use Psr\Http\Message\RequestInterface;

final class AddRequiredVkParametersToQuery
{
    public function __invoke(string $vkApiVersion, string $accessToken): callable
    {
        return fn (callable $next): callable => (
            fn (RequestInterface $request, array $options): Promise => $next(
                $this->addRequiredParamsToRequest($request, $vkApiVersion, $accessToken),
                $options
            )
        );
    }

    private function addRequiredParamsToRequest(RequestInterface $request, string $vkApiVersion, string $accessToken): RequestInterface
    {
        /** @var array<string, mixed> $queryParams */
        $queryParams = [];

        parse_str($request->getUri()->getQuery(), $queryParams);

        $uriWithRequiredParams = $request
            ->getUri()
            ->withQuery(http_build_query($this->addRequiredParamsToQueryParams($queryParams, $vkApiVersion, $accessToken)));

        return $request->withUri($uriWithRequiredParams);
    }

    private function addRequiredParamsToQueryParams(array $queryParams, string $vkApiVersion, string $accessToken): array
    {
        return array_merge($queryParams, ['v' => $vkApiVersion, 'access_token' => $accessToken]);
    }
}
