<?php declare(strict_types=1);

namespace Shopware\Storefront\Theme\Twig;

use Shopware\Core\Framework\Adapter\Twig\NamespaceHierarchy\TemplateNamespaceHierarchyBuilderInterface;
use Shopware\Core\SalesChannelRequest;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ThemeNamespaceHierarchyBuilder implements TemplateNamespaceHierarchyBuilderInterface, EventSubscriberInterface
{
    private array $themes = [];

    private ThemeInheritanceBuilderInterface $themeInheritanceBuilder;

    public function __construct(ThemeInheritanceBuilderInterface $themeInheritanceBuilder)
    {
        $this->themeInheritanceBuilder = $themeInheritanceBuilder;
    }

    /**
     * @return array<string, string|array{0: string, 1: int}|list<array{0: string, 1?: int}>>
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'requestEvent',
            KernelEvents::EXCEPTION => 'requestEvent',
        ];
    }

    public function requestEvent(RequestEvent $event): void
    {
        $request = $event->getRequest();

        $this->themes = $this->detectedThemes($request);
    }

    public function buildNamespaceHierarchy(array $namespaceHierarchy): array
    {
        if (empty($this->themes)) {
            return $namespaceHierarchy;
        }

        return $this->themeInheritanceBuilder->build($namespaceHierarchy, $this->themes);
    }

    private function detectedThemes(Request $request): array
    {
        // get name if theme is not inherited
        $theme = $request->attributes->get(SalesChannelRequest::ATTRIBUTE_THEME_NAME);

        if (!$theme) {
            // get theme name from base theme because for inherited themes the name is always null
            $theme = $request->attributes->get(SalesChannelRequest::ATTRIBUTE_THEME_BASE_NAME);
        }

        if (!$theme) {
            return [];
        }

        $themes[$theme] = true;
        $themes['Storefront'] = true;

        return $themes;
    }
}
