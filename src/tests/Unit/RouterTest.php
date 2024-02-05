<?php
declare(strict_types=1);

namespace tests\Unit;

use App\Exceptions\RouteNotFoundException;
use App\Routing\Router;
use PHPUnit\Framework\TestCase;

class RouterTest extends TestCase
{
    private Router $router;

    protected function setUp(): void
    {
        parent::setUp();

        $this->router = new Router();
    }

    public function testThatItRegisterARoute(): void
    {
        $this->router->registerRoute('get', '/subscribers', ['Subscribers', 'index']);
        $expected = [
            'get' => [
                '/subscribers' => ['Subscribers', 'index']
            ]
        ];
        $this->assertEquals($expected, $this->router->routes());
    }

    public function testThatItRegisterAGetRoute(): void
    {
        $this->router->get('/subscribers', ['Subscribers', 'index']);
        $expected = [
            'get' => [
                '/subscribers' => ['Subscribers', 'index']
            ]
        ];
        $this->assertEquals($expected, $this->router->routes());
    }

    public function testThatItRegisterAPostRoute(): void
    {
        $this->router->post('/subscribers', ['Subscribers', 'store']);
        $expected = [
            'post' => [
                '/subscribers' => ['Subscribers', 'store']
            ]
        ];
        $this->assertEquals($expected, $this->router->routes());
    }

    public function testNoRoutesWhenRouterIsCreated(): void
    {
        $this->assertEmpty((new Router())->routes());
    }

    /**
     * @dataProvider routeNotFoundCases
     */
    public function testThatItThrowsARouterNotFoundException(
        string $requestURI, string $requestMethod
    ): void
    {
        $users = new Class() {
            public function delete(): bool
            {
                return true;
            }
        };

        $this->router->post('/subscriber', [$users::class, 'store']);
        $this->router->get('/subscriber', ['Subscribers', 'index']);

        $this->expectException(RouteNotFoundException::class);

        $this->router->resolveRoute($requestURI, $requestMethod);
    }

    public function routeNotFoundCases(): array
    {
        return [
            ['/subscriber', 'put'],
            ['/api', 'post'],
            ['/subscriber', 'get'],
            ['/subscriber', 'post']
        ];
    }

    public function testItResolvesRouteFromClosure(): void
    {
        $this->router->get('/subscriber', fn()=> [1,2,3]);
        $this->assertEquals([1,2,3], $this->router->resolveRoute('/subscriber', 'get'));
    }

    public function testItResolvesRoute(): void
    {
        $users = new Class() {
            public function index(): array
            {
                return [1,2,3];
            }
        };

        $this->router->get('/subscriber', [$users::class, 'index']);
        $this->assertEquals([1,2,3], $this->router->resolveRoute('/subscriber', 'get'));
    }
}