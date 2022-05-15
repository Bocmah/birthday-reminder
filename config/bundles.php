<?php

declare(strict_types=1);

use Baldinof\RoadRunnerBundle\BaldinofRoadRunnerBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;

return [
    FrameworkBundle::class => ['all' => true],
    BaldinofRoadRunnerBundle::class => ['all' => true],
];
