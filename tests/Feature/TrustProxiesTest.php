<?php

namespace Tests\Feature;

use Tests\TestCase;

class TrustProxiesTest extends TestCase
{
    public function test_request_is_secure_when_proxy_forwards_https(): void
    {
        $this->get('/up', ['X-Forwarded-Proto' => 'https']);

        $this->assertTrue(request()->isSecure());
        $this->assertStringStartsWith('https://', url('/'));
    }
}
