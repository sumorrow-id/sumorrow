<?php

namespace Tests\Unit\Middleware;

use App\Http\Middleware\AdminMiddleware;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AdminMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    private AdminMiddleware $middleware;

    protected function setUp(): void
    {
        parent::setUp();
        $this->middleware = new AdminMiddleware();
    }

    /**
     * The closure passed to handle() is what runs when the middleware decides to
     * forward the request. Returning a sentinel Response lets us assert pass-through.
     */
    private function nextSentinel(): \Closure
    {
        return fn (Request $request) => new Response('OK', 200);
    }

    // -------------------------------------------------------------------------
    // Pass-through: authenticated admin
    // -------------------------------------------------------------------------

    public function test_passes_request_through_for_admin_user(): void
    {
        $admin = User::factory()->admin()->create();
        Auth::login($admin);

        $response = $this->middleware->handle(Request::create('/admin/dashboard'), $this->nextSentinel());

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('OK', $response->getContent());
    }

    // -------------------------------------------------------------------------
    // Block: authenticated non-admin
    // -------------------------------------------------------------------------

    public function test_redirects_authenticated_non_admin_user_to_root(): void
    {
        $user = User::factory()->create(); // default role is 'user'
        Auth::login($user);

        $response = $this->middleware->handle(Request::create('/admin/dashboard'), $this->nextSentinel());

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(url('/'), $response->getTargetUrl());
    }

    public function test_flashes_unauthorized_error_when_blocking_non_admin(): void
    {
        $user = User::factory()->create();
        Auth::login($user);

        $response = $this->middleware->handle(Request::create('/admin/dashboard'), $this->nextSentinel());

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame('Unauthorized access.', $response->getSession()->get('error'));
    }

    // -------------------------------------------------------------------------
    // Block: guest
    // -------------------------------------------------------------------------

    public function test_redirects_guest_to_root(): void
    {
        // No Auth::login — request is anonymous.
        $response = $this->middleware->handle(Request::create('/admin/dashboard'), $this->nextSentinel());

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(url('/'), $response->getTargetUrl());
    }

    // -------------------------------------------------------------------------
    // Block: user whose role is something other than 'admin'
    // -------------------------------------------------------------------------

    public function test_redirects_user_with_unexpected_role(): void
    {
        $user = User::factory()->create(['role' => 'moderator']);
        Auth::login($user);

        $response = $this->middleware->handle(Request::create('/admin/dashboard'), $this->nextSentinel());

        $this->assertInstanceOf(RedirectResponse::class, $response);
        $this->assertSame(url('/'), $response->getTargetUrl());
    }
}
