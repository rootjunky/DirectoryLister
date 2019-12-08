<?php

namespace App\Controllers;

use PHLAK\Config\Config;
use Slim\Psr7\Response;
use Slim\Views\Twig;
use Symfony\Component\Finder\Finder;
use Tightenco\Collect\Support\Collection;

class DirectoryController
{
    /** @var Config App configuration component */
    protected $config;

    /** @var Twig Twig templating component */
    protected $view;

    /**
     * Create a new DirectoryController object.
     *
     * @param \PHLAK\Config\Config $config
     * @param \Slim\Views\Twig     $view
     */
    public function __construct(Config $config, Twig $view)
    {
        $this->config = $config;
        $this->view = $view;
    }

    /**
     * Invoke the DirectoryController.
     *
     * @param \Symfony\Component\Finder\Finder $finder
     * @param \Slim\Psr7\Response              $response
     * @param string                           $path
     *
     * @return \Psr\Http\Message\ResponseInterface
     */
    public function __invoke(Finder $files, Response $response, string $path = '.')
    {
        return $this->view->render($response, 'index.twig', [
            'breadcrumbs' => $this->breadcrumbs($path),
            'files' => $files->in($path),
        ]);
    }

    /**
     * Build an array of breadcrumbs for a given path.
     *
     * @param string $path
     *
     * @return array
     */
    protected function breadcrumbs(string $path): array
    {
        $breadcrumbs = Collection::make(array_filter(explode('/', $path)));

        return $breadcrumbs->filter(function (string $crumb) {
            return $crumb !== '.';
        })->reduce(function (array $carry, string $crumb) {
            $carry[$crumb] = end($carry) . "/{$crumb}";

            return $carry;
        }, []);
    }
}