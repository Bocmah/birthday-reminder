<?php

declare(strict_types=1);

namespace Vkbd;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
final class TestController extends AbstractController
{
    #[Route('/test', methods: ['GET'])]
    public function test(): Response
    {
        return $this->json('test');
    }
}
