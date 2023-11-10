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

class LuckyController extends AbstractController
{
    #[Route('/lucky')]
    public function number(): Response
    {
        $number = random_int(0, 100);

        return $this->render(
            'lucky.html.twig',
            ['number' => $number]
        );
    }

    #[Route("/lucky/hi")]
    public function hi(): Response
    {
        return new Response(
            '<html><body>Hi to you!</body></html>'
        );
    }

    #[Route("/api/quote", name: "quote")]
    public function jsonQuote(): Response
    {


        $quotes = array(
        "Försök att göra någon glad varje dag, om det så bara är dig själv. - Okänd",
        "För mycket av det goda kan vara underbart. - Mae West",
        "Det viktigaste är inte varifrån man kommer utan vart man är på väg. - Bernie Rhodes");
        shuffle($quotes);
        foreach ($quotes as $quote) {
            //u($quote)->normalize(UnicodeString::NFC);
        }
        $currentDate = date('Y-m-d h:i:s');

        $data = [

            'Citat' => $quotes[0],
            'Datum' => $currentDate,
        ];


        $response = new JsonResponse($data);
        $response->setEncodingOptions($response->getEncodingOptions() | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return $response;
        //return $this->json($data);
    }

    #[Route("/about", name: "about")]
    public function about(): Response
    {
        return $this->render('about.html.twig');
    }

    #[Route("/", name: "me")]
    public function me(): Response
    {
        return $this->render('me.html.twig');
    }

    #[Route("/report", name: "report")]
    public function report(): Response
    {
        return $this->render('report.html.twig');
    }


    #[Route("/lucky", name: "lucky")]
    public function lucky(): Response
    {
        return $this->render('lucky.html.twig');
    }
}
