<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\String\UnicodeString;
use Normalizer;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

use function Symfony\Component\String\u;

class MetricsController extends AbstractController
{
    #[Route('/metrics', name: "metrics")]
    public function metricsView(): Response
    {
        return $this->render('metrics/metrics.html.twig');
    }
}
