<?php

namespace eCamp\Lib\Hal\Factory;

use eCamp\Lib\Hal\Extractor\LinkExtractor;
use Laminas\ApiTools\Hal\Link\LinkUrlBuilder;
use Laminas\Mvc\Application;
use Laminas\Router\RouteMatch;
use Laminas\View\Helper\ServerUrl;
use Laminas\View\Helper\Url;

class LinkExtractorFactory {
    /**
     * @param \Interop\Container\ContainerInterface|\Laminas\ServiceManager\ServiceLocatorInterface $container
     *
     * @return LinkExtractor
     */
    public function __invoke($container) {
        /** @var Application $application */
        $application = $container->get('Application');
        /** @var RouteMatch $routeMatch */
        $routeMatch = $application->getMvcEvent()->getRouteMatch();

        $viewHelperManager = $container->get('ViewHelperManager');
        /** @var ServerUrl $serverUrlHelper */
        $serverUrlHelper = $viewHelperManager->get('ServerUrl');
        /** @var Url $urlHelper */
        $urlHelper = $viewHelperManager->get('Url');

        $linkExtractor = new LinkExtractor($container->get(LinkUrlBuilder::class));
        $linkExtractor->setRouter($container->get('Router'));
        $linkExtractor->setRouteMatch($routeMatch);
        $linkExtractor->setServerUrl($serverUrlHelper);
        $linkExtractor->setUrl($urlHelper);

        if (isset($container->get('Config')['api-tools-rest'])) {
            $linkExtractor->setZfRestConfig($container->get('Config')['api-tools-rest']);
        }
        if (isset($container->get('Config')['api-tools-rpc'])) {
            $linkExtractor->setRpcConfig($container->get('Config')['api-tools-rpc']);
        }

        return $linkExtractor;
    }
}
