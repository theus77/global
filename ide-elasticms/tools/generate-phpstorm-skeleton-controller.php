<?php

declare(strict_types=1);

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderResolver;
use Symfony\Component\Routing\Loader\PhpFileLoader;
use Symfony\Component\Routing\Loader\XmlFileLoader;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;
use Symfony\Component\Yaml\Yaml;

require __DIR__.'/../vendor/autoload.php';

final class BundleAwareFileLocator extends FileLocator
{
    /**
     * @param array<string, string> $bundlePaths
     */
    public function __construct(private readonly array $bundlePaths)
    {
        parent::__construct();
    }

    public function locate(string $name, ?string $currentPath = null, bool $first = true): string|array
    {
        if (\str_starts_with($name, '@')) {
            [$bundleName, $resource] = \explode('/', \substr($name, 1), 2);
            if (!isset($this->bundlePaths[$bundleName])) {
                throw new RuntimeException(\sprintf('Unknown bundle resource: %s', $name));
            }

            return parent::locate($this->bundlePaths[$bundleName].'/'.$resource, $currentPath, $first);
        }

        return parent::locate($name, $currentPath, $first);
    }
}

$skeletonDir = '/workspace/skeleton';
$routesFile = $skeletonDir.'/routes.yaml';
$target = __DIR__.'/../src/PhpStorm/GeneratedSkeletonController.php';

if (!\is_file($routesFile)) {
    throw new RuntimeException(\sprintf('Routes file not found: %s', $routesFile));
}

/** @var array<string, array{config?: array<string, mixed>}> $routes */
\is_array($skeletonRoutes = Yaml::parseFile($routesFile)) || throw new RuntimeException(\sprintf('Invalid routes structure in %s', $routesFile));
$routes = $skeletonRoutes;
$routes = \array_replace($routes, bundleRoutes());
\ksort($routes);

\is_dir(\dirname($target)) || \mkdir(\dirname($target), 0777, true);
\file_put_contents($target, renderController($routes));

echo \sprintf("Generated %s\n", $target);

/**
 * @return array<string, array{config: array<string, mixed>}>
 */
function bundleRoutes(): array
{
    $bundlePaths = bundlePaths();
    $resources = [
        'EMSClientHelperBundle' => [
            'config/routing/core_bridge.php',
            'config/routing/user_api.php',
        ],
        'EMSCommonBundle' => [
            'config/routing/assets.php',
            'config/routing/file.php',
            'config/routing/probe.php',
        ],
        'EMSCoreBundle' => [
            'config/routing/all.php',
        ],
        'EMSFormBundle' => [
            'config/routing/debug.php',
            'config/routing/form.php',
        ],
        'EMSSubmissionBundle' => [],
    ];

    $locator = new BundleAwareFileLocator($bundlePaths);
    $phpLoader = new PhpFileLoader($locator);
    $xmlLoader = new XmlFileLoader($locator);
    $resolver = new LoaderResolver([$phpLoader, $xmlLoader]);
    $phpLoader->setResolver($resolver);
    $xmlLoader->setResolver($resolver);

    $routes = [];
    foreach ($resources as $bundleName => $files) {
        foreach ($files as $file) {
            if (!isset($bundlePaths[$bundleName])) {
                continue;
            }

            $collection = $phpLoader->load('@'.$bundleName.'/'.$file);
            foreach ($collection->all() as $name => $route) {
                $routes[$name] = [
                    'config' => routeConfig($route),
                ];
            }
        }
    }

    return $routes;
}

/**
 * @return array<string, string>
 */
function bundlePaths(): array
{
    $classes = [
        EMS\ClientHelperBundle\EMSClientHelperBundle::class,
        EMS\CommonBundle\EMSCommonBundle::class,
        EMS\CoreBundle\EMSCoreBundle::class,
        EMS\FormBundle\EMSFormBundle::class,
        EMS\SubmissionBundle\EMSSubmissionBundle::class,
    ];

    $paths = [];
    foreach ($classes as $class) {
        if (!\class_exists($class)) {
            continue;
        }

        $reflection = new ReflectionClass($class);
        $paths[$reflection->getShortName()] = \dirname($reflection->getFileName(), 2);
    }

    return $paths;
}

/**
 * @return array<string, mixed>
 */
function routeConfig(Route $route): array
{
    $config = [
        'path' => $route->getPath(),
    ];

    if ([] !== $defaults = \array_diff_key($route->getDefaults(), ['_controller' => true])) {
        $config['defaults'] = $defaults;
    }
    if ([] !== $requirements = $route->getRequirements()) {
        $config['requirements'] = $requirements;
    }
    if ('' !== $host = $route->getHost()) {
        $config['host'] = $host;
    }
    if ([] !== $schemes = $route->getSchemes()) {
        $config['schemes'] = $schemes;
    }
    if ([] !== $methods = $route->getMethods()) {
        $config['methods'] = $methods;
    }
    if (null !== $condition = $route->getCondition()) {
        $config['condition'] = $condition;
    }
    if (null !== $format = $route->getDefault('_format')) {
        $config['format'] = $format;
    }
    if ($route->hasOption('utf8')) {
        $config['utf8'] = $route->getOption('utf8');
    }
    if ($route->hasOption('compiler_class')) {
        unset($config['compiler_class']);
    }

    return $config;
}

/**
 * @param array<string, array{config?: array<string, mixed>}> $routes
 */
function renderController(array $routes): string
{
    $methods = [];
    $usedMethodNames = [];

    foreach ($routes as $name => $route) {
        $config = $route['config'] ?? [];
        if (!\is_array($config) || !isset($config['path'])) {
            continue;
        }

        $methods[] = renderMethod($name, $config, uniqueMethodName($name, $usedMethodNames));
    }

    $methodsBlock = \implode("\n\n", $methods);

    return <<<PHP
<?php

declare(strict_types=1);

namespace App\\PhpStorm;

use Symfony\\Component\\HttpFoundation\\Response;
use Symfony\\Component\\Routing\\Attribute\\Route;

/**
 * Generated by tools/generate-phpstorm-skeleton-controller.php from /workspace/skeleton/routes.yaml.
 */
final class GeneratedSkeletonController
{
$methodsBlock
}

PHP;
}

/**
 * @param array<string, mixed> $config
 */
function renderMethod(string $name, array $config, string $methodName): string
{
    $arguments = ["name: ".exportValue($name)];

    foreach (['path', 'defaults', 'requirements', 'host', 'schemes', 'methods', 'condition', 'priority', 'env', 'format', 'utf8', 'stateless'] as $key) {
        if (\array_key_exists($key, $config)) {
            $arguments[] = $key.': '.exportValue($config[$key]);
        }
    }

    $parameters = [];
    foreach (methodParameters($config) as $parameter) {
        $parameters[] = renderParameter($parameter);
    }

    $signature = [] === $parameters ? '' : \implode(', ', $parameters);
    $attribute = '#[Route('.\implode(', ', $arguments).')]';

    return <<<PHP
    $attribute
    public function $methodName($signature): Response
    {
        return new Response();
    }
PHP;
}

function methodName(string $routeName): string
{
    $name = \preg_replace('/[^a-zA-Z0-9]+/', ' ', $routeName) ?? $routeName;
    $name = \str_replace(' ', '', \ucwords(\trim($name)));
    $name = '' === $name ? 'generatedRoute' : \lcfirst($name);

    if (\is_numeric($name[0])) {
        $name = 'route'.$name;
    }

    return $name;
}

/**
 * @param array<string, true> $usedMethodNames
 */
function uniqueMethodName(string $routeName, array &$usedMethodNames): string
{
    $baseName = methodName($routeName);
    $name = $baseName;
    $suffix = 2;

    while (isset($usedMethodNames[$name])) {
        $name = $baseName.$suffix;
        ++$suffix;
    }

    $usedMethodNames[$name] = true;

    return $name;
}

/**
 * @param array<string, mixed> $config
 *
 * @return array<int, array{name: string, default: mixed, hasDefault: bool}>
 */
function methodParameters(array $config): array
{
    $names = [];
    foreach ((array) ($config['path'] ?? []) as $path) {
        if (!\is_string($path)) {
            continue;
        }

        if (\preg_match_all('/\{([A-Za-z_][A-Za-z0-9_]*)\}/', $path, $matches)) {
            foreach ($matches[1] as $match) {
                $names[$match] = true;
            }
        }
    }

    $defaults = \is_array($config['defaults'] ?? null) ? $config['defaults'] : [];
    $parameters = [];

    foreach (\array_keys($names) as $name) {
        $parameters[] = [
            'name' => $name,
            'default' => $defaults[$name] ?? null,
            'hasDefault' => \array_key_exists($name, $defaults),
        ];
    }

    \usort($parameters, static function (array $left, array $right): int {
        return ($left['hasDefault'] <=> $right['hasDefault']) ?: 0;
    });

    return $parameters;
}

/**
 * @param array{name: string, default: mixed, hasDefault: bool} $parameter
 */
function renderParameter(array $parameter): string
{
    $name = '$'.$parameter['name'];
    if (!$parameter['hasDefault']) {
        return 'string '.$name;
    }

    $default = $parameter['default'];
    if (\is_int($default)) {
        return 'int '.$name.' = '.exportValue($default);
    }
    if (\is_bool($default)) {
        return 'bool '.$name.' = '.exportValue($default);
    }
    if (null === $default) {
        return '?string '.$name.' = null';
    }

    return 'string '.$name.' = '.exportValue((string) $default);
}

function exportValue(mixed $value): string
{
    if (\is_array($value)) {
        return shortArray($value);
    }

    return \var_export($value, true);
}

/**
 * @param array<mixed> $array
 */
function shortArray(array $array): string
{
    $items = [];
    foreach ($array as $key => $value) {
        $renderedValue = \is_array($value) ? shortArray($value) : \var_export($value, true);

        if (\is_int($key)) {
            $items[] = $renderedValue;
            continue;
        }

        $items[] = \var_export($key, true).' => '.$renderedValue;
    }

    return '['.\implode(', ', $items).']';
}
