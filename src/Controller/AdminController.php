<?php

namespace App\Controller;

use App\Service\YoutubeDl\Downloader;
use App\Service\YoutubeDl\Format;
use App\Service\YoutubeDl\Installer;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(Installer $installer): Response
    {
        return $this->render('admin/index.html.twig', [
            'isInstalled' => $installer->isInstalled(),
            'version' => $installer->isInstalled() ? $installer->version() : null,
            'availableVersion' => $installer->availableVersion(),
        ]);
    }

    /**
     * @Route("/install", name="install")
     */
    public function installYoutubeDl(Installer $youtubeDlService): Response
    {
        $youtubeDlService->install();

        return $this->redirectToRoute('index');
    }

    /**
     * @Route("/analyze", name="analyze")
     */
    public function analyze(Request $request, Downloader $downloader): Response
    {
        $analyzeForm = $this->createFormBuilder()
            ->add('url', TextType::class)
            ->add('submit', SubmitType::class)
            ->getForm();

        $analyzeForm->handleRequest($request);

        $tracks = [];
        $formats = [];
        if ($analyzeForm->isSubmitted() && $analyzeForm->isValid()) {
            $url = $analyzeForm->getData()['url'];
            $tracks = $downloader->getTracksFromUrl($url);
            $formats = $downloader->getFormats($url);
        }

        $downloadForm = $this->createFormBuilder()
            ->add('format', ChoiceType::class, [
                'choices' => $formats,
                'choice_value' => 'code',
                'choice_label' => 'description',
                'group_by' => function (Format $format) {
                    return $format->isAudioOnly() ? 'Audio' : 'Video';
                },
                'preferred_choices' => function (Format $format) {
                    return $format->isAudioOnly();
                },
            ])
            ->getForm();

        return $this->render('admin/analyze.html.twig', [
            'form' => $analyzeForm->createView(),
            'downloadForm' => $downloadForm->createView(),
            'tracks' => $tracks,
            'formats' => $formats,
        ]);
    }
}
