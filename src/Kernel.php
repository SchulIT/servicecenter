<?php

declare(strict_types=1);

namespace App;

use Override;
use App\DependencyInjection\Compiler\RemovePcntlEventSubscriberPass;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    #[Override]
    public function build(ContainerBuilder $container): void {
        $container->addCompilerPass(new RemovePcntlEventSubscriberPass());
    }
}
