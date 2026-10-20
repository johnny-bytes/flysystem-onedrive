<?php

namespace Justus\FlysystemOneDrive;

use Closure;
use Microsoft\Graph\Graph;

/**
 * A Graph client that sets the current access token on every request.
 *
 * A queue worker keeps a disk for hours, past the token's lifetime, so the
 * token cannot be set once when the disk is built.
 */
class RefreshingGraph extends Graph
{
    /** @param  Closure(): string  $resolveAccessToken */
    public function __construct(private readonly Closure $resolveAccessToken)
    {
        parent::__construct();

        $this->refreshAccessToken();
    }

    public function createRequest($requestType, $endpoint)
    {
        $this->refreshAccessToken();

        return parent::createRequest($requestType, $endpoint);
    }

    public function createCollectionRequest($requestType, $endpoint)
    {
        $this->refreshAccessToken();

        return parent::createCollectionRequest($requestType, $endpoint);
    }

    private function refreshAccessToken(): void
    {
        $this->setAccessToken(($this->resolveAccessToken)());
    }
}
