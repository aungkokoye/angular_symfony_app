<?php

namespace App;

use App\Infrastructure\DependencyInjection\DoctrineMappingPass;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    protected function build(ContainerBuilder $container): void
    {
        parent::build($container);

        // DoctrineMappingPass will register Domain folder's entities orm mapping configurations automatically
        $container->addCompilerPass(new DoctrineMappingPass());
    }
}
