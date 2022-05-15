<?php

declare(strict_types=1);

namespace Vkbd\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

final class TestController extends AbstractController
{
    #[Route('/test', methods: ['GET'])]
    public function test(): Response
    {
        return $this->json('test');
    }
}
