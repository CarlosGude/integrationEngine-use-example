<?php

return [
    'ServicesBundle' => new \Symfony\Component\DependencyInjection\Kernel\ServicesBundle(),
    'ConsoleBundle' => new \Symfony\Component\Console\ConsoleBundle(),
    'FrameworkBundle' => new \Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
    'IntegrationEngineBundle' => new \IntegrationEngine\Bundle\IntegrationEngineBundle(),
    'MakerBundle' => new \Symfony\Bundle\MakerBundle\MakerBundle(),
    'DoctrineBundle' => new \Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
    'DoctrineMigrationsBundle' => new \Doctrine\Bundle\MigrationsBundle\DoctrineMigrationsBundle(),
];
