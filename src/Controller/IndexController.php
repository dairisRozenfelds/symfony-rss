<?php

namespace App\Controller;

use App\Tools\RssParsers\TheRegisterParser;
use DateTime;
use Psr\Cache\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\ItemInterface;

class IndexController extends AbstractController
{
    /**
     * @Route("/index", name="index")
     * @param TheRegisterParser $theRegisterParser
     * @return RedirectResponse|Response
     * @throws InvalidArgumentException
     */
    public function index(TheRegisterParser $theRegisterParser)
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login_get');
        }

        $cache = new FilesystemAdapter();

        // Cache the entries
        $rssData = $cache->get('rss_data', function (ItemInterface $item) use ($theRegisterParser) {
            $expirationDateTime = new DateTime();
            $expirationDateTime->modify('+4 hours'); // Invalidate the entry from cache after 4 hours

            $item->expiresAt($expirationDateTime);

            return $theRegisterParser->parse();
        });

        return $this->render('index/index.html.twig', [
            'rssData' => $rssData
        ]);
    }
}
