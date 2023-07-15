<?php

namespace App\Twig;

use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class RequestExtension extends AbstractExtension
{
    private RequestStack $requestStack;

    private string $route;
    private string $method;
    private string $controller;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('get_route', [$this, 'getRoute']),
            new TwigFunction('get_method', [$this, 'getMethod']),
            new TwigFunction('get_controller', [$this, 'getController']),
            new TwigFunction('is_route', [$this, 'isRoute']),
            new TwigFunction('is_method', [$this, 'isMethod']),
            new TwigFunction('is_controller', [$this, 'isController'])
        ];
    }

    public function getRoute(): string
    {
        if (empty($this->route)) {
            $this->route = $this->requestStack->getCurrentRequest()->attributes->get('_route');
        }

        return $this->route;
    }

    public function getMethod(): string
    {
        if (empty($this->method)) {
            preg_match("/Controller::(.*)$/", $this->requestStack->getCurrentRequest()->attributes->get('_controller'), $matches);
            $this->method = $matches[1];
        }

        return $this->method;
    }

    public function getController(): string
    {
        if (empty($this->controller)) {
            preg_match("/\\Controller\\\(.*)::/", $this->requestStack->getCurrentRequest()->attributes->get('_controller'), $matches);
            $this->controller = $matches[1];
        }

        return $this->controller;
    }

    public function isRoute(string $route): bool
    {
        return $this->getRoute() === $route;
    }

    public function isMethod(string $method): bool
    {
        return $this->getMethod() === $method;
    }

    public function isController(string $controller): bool
    {
        return $this->getController() === $controller;
    }
}
