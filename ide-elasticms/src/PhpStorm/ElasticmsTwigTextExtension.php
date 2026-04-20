<?php

declare(strict_types=1);

namespace App\PhpStorm;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * IDE-only mirror for ElasticMS Twig attributes.
 *
 * Symfony loads EMS\CommonBundle\Twig\TextExtension through Twig's
 * AttributeExtension. PhpStorm does not always expose those filters in Twig
 * completion, so this classic extension shape keeps template completion aware
 * of the available names. This class is excluded from Symfony services.
 */
final class ElasticmsTwigTextExtension extends AbstractExtension
{
    /**
     * @return list<TwigFilter>
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('ems_ascii_folding', [$this, 'ideOnly']),
            new TwigFilter('ems_dom_crawler', [$this, 'ideOnly']),
            new TwigFilter('ems_html_decode', [$this, 'ideOnly']),
            new TwigFilter('ems_html_encode', [$this, 'ideOnly']),
            new TwigFilter('ems_anti_spam', [$this, 'ideOnly']),
            new TwigFilter('ems_valid_mail', [$this, 'ideOnly']),
            new TwigFilter('ems_json_decode', [$this, 'ideOnly']),
            new TwigFilter('ems_json_menu_decode', [$this, 'ideOnly']),
            new TwigFilter('ems_json_menu_nested_decode', [$this, 'ideOnly']),
            new TwigFilter('ems_markdown', [$this, 'ideOnly']),
            new TwigFilter('ems_preg_match', [$this, 'ideOnly']),
            new TwigFilter('ems_replace_regex', [$this, 'ideOnly']),
            new TwigFilter('ems_slug', [$this, 'ideOnly']),
        ];
    }

    /**
     * @return list<TwigFunction>
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('ems_html', [$this, 'ideOnly']),
        ];
    }

    /**
     * @param mixed ...$arguments
     */
    public function ideOnly(mixed ...$arguments): mixed
    {
        return null;
    }
}
