<?php

namespace App\Infrastructure\DependencyInjection;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Finder\Finder;
use Doctrine\Bundle\DoctrineBundle\DependencyInjection\Compiler\DoctrineOrmMappingsPass;
use Doctrine\ORM\Mapping\Driver\AttributeDriver;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Automatically discovers and registers Doctrine entities from Domain-Driven Design structure.
 *
 * This compiler pass enables organizing entities in domain-specific folders like:
 * - src/Domain/Student/Entity
 * - src/Domain/Teacher/Entity
 * - src/Domain/Subject/Entity
 *
 * Without this pass, you would need to manually configure each entity path in doctrine.yaml.
 * This pass automatically scans for 'Entity' folders under src/Domain and registers them.
 */
class DoctrineMappingPass implements CompilerPassInterface
{
    /**
     * Scans the Domain directory and registers all entity namespaces with Doctrine.
     *
     * Process:
     * 1. Search for all directories named 'Entity' under src/Domain/
     * 2. Extract domain name (e.g., 'Student', 'Teacher') from parent directory
     * 3. Build namespace like 'App\Domain\Student\Entity'
     * 4. Register with Doctrine ORM so entities are automatically discovered
     *
     * @param ContainerBuilder $container The DI container being built during compilation
     */
    public function process(ContainerBuilder $container): void
    {
        // Result: When 'app.my_custom_service' is created, Symfony automatically calls setLogger($loggerInstance)
//         $definition = $container->getDefinition('app.my_custom_service');
//         $definition->addMethodCall('setLogger', [new Reference('logger')]);

        $projectDir = $container->getParameter('kernel.project_dir');
        $finder     = new Finder();

        // Scan for all 'Entity' folders inside src/Domain/
        // Example matches: src/Domain/Student/Entity, src/Domain/Teacher/Entity
        $finder->directories()->in($projectDir . '/src/Domain/')->name('Entity');

        $namespaces = [];
        $directories = [];

        // Convert each found directory into namespace + path pair
        foreach ($finder as $directory) {
            // Get domain name: 'src/Domain/Student/Entity' -> 'Student'
            $domain = basename(dirname($directory));

            // Build PSR-4 namespace: 'Student' -> 'App\Domain\Student\Entity'
            $namespace = 'App\Domain\\' . $domain . '\\Entity';

            $namespaces[] = $namespace;
            $directories[] = $directory->getRealPath();
        }

        if (empty($namespaces)) {
            return;
        }

        // Manually create AttributeDriver to avoid Doctrine ORM 3.x compatibility issue.
        //
        // Problem: DoctrineBundle's createAttributeMappingDriver() helper passes
        // the deprecated $reportFieldsWhereDeclared parameter which was removed in
        // Doctrine ORM 3.0, causing a fatal error.
        //
        // Solution: Create the Definition ourselves with only the $directories parameter.
        $driver = new Definition(AttributeDriver::class, [$directories]);

        // Register the mappings with Doctrine ORM
        $mappingPass = new DoctrineOrmMappingsPass(
            $driver,               // AttributeDriver that reads PHP 8 attributes from entity classes
            $namespaces,           // Namespaces to scan (e.g., ['App\Domain\Student\Entity'])
            [],                    // Manager parameters (empty = use default entity manager)
            false,  // Enabled parameter (false = always enabled)
            []                     // Alias map (empty = no namespace aliases)
        );

        $mappingPass->process($container);
    }
}
