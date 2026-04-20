<?php

declare(strict_types=1);

use Twig\Attribute\AsTwigFilter;
use Twig\Attribute\AsTwigFunction;
use Twig\Attribute\AsTwigTest;

require __DIR__.'/../vendor/autoload.php';

$projectDir = \dirname(__DIR__);
$packages = [
    'elasticms/client-helper-bundle',
    'elasticms/common-bundle',
    'elasticms/core-bundle',
    'elasticms/form-bundle',
    'elasticms/submission-bundle',
];

$callables = [
    'filters' => [],
    'functions' => [],
    'tests' => [],
];

foreach ($packages as $package) {
    $composerFile = $projectDir.'/vendor/'.$package.'/composer.json';
    if (!\is_file($composerFile)) {
        continue;
    }

    $composer = \json_decode((string) \file_get_contents($composerFile), true, flags: JSON_THROW_ON_ERROR);
    foreach (($composer['autoload']['psr-4'] ?? []) as $namespace => $paths) {
        foreach ((array) $paths as $path) {
            $sourceDir = $projectDir.'/vendor/'.$package.'/'.\rtrim((string) $path, '/').'/Twig';
            if (!\is_dir($sourceDir)) {
                continue;
            }

            foreach (phpFiles($sourceDir) as $file) {
                $class = className($namespace, $sourceDir, $file);
                if (null === $class || !\class_exists($class)) {
                    continue;
                }

                scanClass($class, $callables);
            }
        }
    }
}

foreach ($callables as &$items) {
    \ksort($items);
}
unset($items);

$target = $projectDir.'/src/PhpStorm/GeneratedElasticmsTwigAttributeExtension.php';
\is_dir(\dirname($target)) || \mkdir(\dirname($target), 0777, true);
\file_put_contents($target, renderExtension($callables));

echo \sprintf("Generated %s\n", $target);

/**
 * @return iterable<string>
 */
function phpFiles(string $directory): iterable
{
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directory));

    foreach ($iterator as $file) {
        if ($file instanceof SplFileInfo && $file->isFile() && 'php' === $file->getExtension()) {
            yield $file->getPathname();
        }
    }
}

function className(string $namespace, string $sourceDir, string $file): ?string
{
    $relative = \substr($file, \strlen($sourceDir) + 1);
    if (false === $relative || !\str_ends_with($relative, '.php')) {
        return null;
    }

    return \rtrim($namespace, '\\').'\\Twig\\'.\str_replace('/', '\\', \substr($relative, 0, -4));
}

/**
 * @param array{filters: array<string, array{class: class-string, method: string, options: array<string, mixed>}>, functions: array<string, array{class: class-string, method: string, options: array<string, mixed>}>, tests: array<string, array{class: class-string, method: string, options: array<string, mixed>}>} $callables
 */
function scanClass(string $class, array &$callables): void
{
    $reflection = new ReflectionClass($class);
    foreach ($reflection->getMethods() as $method) {
        foreach ($method->getAttributes(AsTwigFilter::class) as $attribute) {
            $instance = $attribute->newInstance();
            $callables['filters'][$instance->name] = [
                'class' => $class,
                'method' => $method->getName(),
                'options' => callableOptions($instance, $method),
            ];
        }

        foreach ($method->getAttributes(AsTwigFunction::class) as $attribute) {
            $instance = $attribute->newInstance();
            $callables['functions'][$instance->name] = [
                'class' => $class,
                'method' => $method->getName(),
                'options' => callableOptions($instance, $method),
            ];
        }

        foreach ($method->getAttributes(AsTwigTest::class) as $attribute) {
            $instance = $attribute->newInstance();
            $callables['tests'][$instance->name] = [
                'class' => $class,
                'method' => $method->getName(),
                'options' => callableOptions($instance, $method),
            ];
        }
    }
}

/**
 * @return array<string, mixed>
 */
function callableOptions(object $attribute, ReflectionMethod $method): array
{
    $options = [
        'needs_context' => $attribute->needsContext ?? false,
        'needs_environment' => $attribute->needsEnvironment ?? needsEnvironment($method),
        'needs_charset' => $attribute->needsCharset ?? false,
        'is_variadic' => $method->isVariadic(),
        'is_safe' => $attribute->isSafe ?? [],
    ];

    if (\property_exists($attribute, 'isSafeCallback')) {
        $options['is_safe_callback'] = $attribute->isSafeCallback;
    }
    if (\property_exists($attribute, 'preEscape')) {
        $options['pre_escape'] = $attribute->preEscape;
    }
    if (\property_exists($attribute, 'preservesSafety')) {
        $options['preserves_safety'] = $attribute->preservesSafety;
    }
    if (\property_exists($attribute, 'deprecationInfo')) {
        $options['deprecation_info'] = $attribute->deprecationInfo;
    }

    return \array_filter($options, static fn (mixed $value): bool => null !== $value && [] !== $value && false !== $value);
}

function needsEnvironment(ReflectionMethod $method): bool
{
    $parameters = $method->getParameters();
    if ([] === $parameters) {
        return false;
    }

    $type = $parameters[0]->getType();

    return $type instanceof ReflectionNamedType
        && Twig\Environment::class === $type->getName()
        && !$parameters[0]->isVariadic();
}

/**
 * @param array{filters: array<string, array{class: class-string, method: string, options: array<string, mixed>}>, functions: array<string, array{class: class-string, method: string, options: array<string, mixed>}>, tests: array<string, array{class: class-string, method: string, options: array<string, mixed>}>} $callables
 */
function renderExtension(array $callables): string
{
    $filters = renderCallables($callables['filters'], 'TwigFilter');
    $functions = renderCallables($callables['functions'], 'TwigFunction');
    $tests = renderCallables($callables['tests'], 'TwigTest');

    return <<<PHP
<?php

declare(strict_types=1);

namespace App\\PhpStorm;

use Twig\\Extension\\AbstractExtension;
use Twig\\TwigFilter;
use Twig\\TwigFunction;
use Twig\\TwigTest;

/**
 * Generated by tools/generate-phpstorm-twig-extension.php.
 */
final class GeneratedElasticmsTwigAttributeExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
$filters
        ];
    }

    public function getFunctions(): array
    {
        return [
$functions
        ];
    }

    public function getTests(): array
    {
        return [
$tests
        ];
    }
}

PHP;
}

/**
 * @param array<string, array{class: class-string, method: string, options: array<string, mixed>}> $callables
 */
function renderCallables(array $callables, string $twigClass): string
{
    $lines = [];
    foreach ($callables as $name => $callable) {
        $arguments = [
            \var_export($name, true),
            '[\\'.$callable['class'].'::class, '.\var_export($callable['method'], true).']',
        ];
        if ([] !== $callable['options']) {
            $arguments[] = shortArray($callable['options']);
        }

        $lines[] = '            new '.$twigClass.'('.\implode(', ', $arguments).'),';
    }

    return \implode("\n", $lines);
}

/**
 * @param array<string, mixed> $array
 */
function shortArray(array $array): string
{
    $items = [];
    foreach ($array as $key => $value) {
        $items[] = \var_export($key, true).' => '.(\is_array($value) ? shortArray($value) : \var_export($value, true));
    }

    return '['.\implode(', ', $items).']';
}
