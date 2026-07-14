<?php

declare(strict_types=1);

namespace Framework\Routing;

final readonly class Route
{
    private mixed $handler;

    public function __construct(private string $path, callable $handler)
    {
        $this->handler = $handler;
    }

    /**
     * @param  array<string, string|null>  $parameters
     */
    public function run(array $parameters): mixed
    {
        return ($this->handler)(...$parameters);
    }

    /**
     * @return array<string, string|null>|null
     */
    public function matches(string $path): ?array
    {
        $routeSegments = explode('/', trim($this->path, '/'));
        $pathSegments = explode('/', trim($path, '/'));

        $lastRouteSegment = array_last($routeSegments);

        $hasOmittedOptionalParameter = $this->hasOmittedOptionalParameter($routeSegments, $pathSegments, $lastRouteSegment);

        $hasTrailingWildcardParameter = $this->isWildcardParameter($lastRouteSegment);

        if (count($routeSegments) !== count($pathSegments) && ! $hasOmittedOptionalParameter && ! $hasTrailingWildcardParameter) {
            return null;
        }

        $parameters = [];

        foreach ($routeSegments as $index => $routeSegment) {
            $pathSegment = $pathSegments[$index] ?? null;

            if ($this->isParameter($routeSegment)) {
                $parameterDefinition = trim($routeSegment, '{}');

                [$parameterName,$constraint] = array_pad(explode(':', $parameterDefinition, 2), 2, null);

                $parameterName = (string) $parameterName;

                $isOptional = $this->isOptional($parameterName);

                if ($isOptional && $index !== array_key_last($routeSegments)) {
                    return null;
                }

                if ($isOptional) {
                    $parameterName = rtrim((string) $parameterName, '?');
                }

                if ($isOptional && $pathSegment === null) {
                    $parameters[$parameterName] = null;

                    continue;
                }

                if ($constraint === '*') {
                    if ($index !== array_key_last($routeSegments)) {
                        return null;
                    }

                    $parameters[$parameterName] = implode('/', array_slice($pathSegments, $index));

                    continue;
                }

                if ($constraint != null && preg_match('#^'.$constraint.'$#', (string) $pathSegment) !== 1) {
                    return null;
                }

                $parameters[$parameterName] = $pathSegment;

                continue;
            }

            if ($routeSegment !== $pathSegment) {
                return null;
            }
        }

        return $parameters;
    }

    private function isParameter(string $segment): bool
    {
        return str_starts_with($segment, '{') && str_ends_with($segment, '}');
    }

    private function isOptional(string $segment): bool
    {
        return str_ends_with((string) $segment, '?');
    }

    /**
     * @param  array<int, string>  $routeSegments
     * @param  array<int, string>  $pathSegments
     */
    private function hasOmittedOptionalParameter(array $routeSegments, array $pathSegments, string $lastRouteSegment): bool
    {
        return count($routeSegments) === count($pathSegments) + 1 && str_starts_with($lastRouteSegment, '{') && str_ends_with($lastRouteSegment, '?}');
    }

    private function isWildcardParameter(string $segment): bool
    {
        return str_starts_with($segment, '{') && str_ends_with($segment, ':*}');
    }
}
