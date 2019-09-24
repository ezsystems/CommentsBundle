<?php

namespace EzSystems\CommentsBundle\Tab;

use eZ\Publish\API\Repository\ContentService;
use eZ\Publish\API\Repository\Values\Content\Content;
use EzSystems\CommentsBundle\Comments\ProviderInterface;
use EzSystems\EzPlatformAdminUi\Tab\AbstractTab;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Environment;

class CommentsTab extends AbstractTab
{
    /** @var \EzSystems\CommentsBundle\Comments\ProviderInterface */
    private $commentsRenderer;

    /** @var \Symfony\Component\HttpFoundation\RequestStack */
    private $requestStack;

    /** @var \eZ\Publish\API\Repository\ContentService */
    private $contentService;

    public function __construct(
        Environment $twig,
        TranslatorInterface $translator,
        ProviderInterface $commentsRenderer,
        ContentService $contentService,
        RequestStack $requestStack
    ) {
        $this->commentsRenderer = $commentsRenderer;
        $this->requestStack = $requestStack;
        $this->contentService = $contentService;

        parent::__construct($twig, $translator);
    }

    /**
     * Returns identifier of the tab.
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'comments-tab';
    }

    /**
     * Returns name of the tab which is displayed as a tab's title in the UI.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->translator->trans('comments.tab.name', [], 'comments');
    }

    /**
     * Returns HTML body of the tab.
     *
     * @param array $parameters
     *
     * @return string
     *
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function renderView(array $parameters): string
    {
        /** @var Content $content */
        $content = $parameters['content'];

        return $this->twig->render('@EzSystemsComments/tab/comments.html.twig', [
            'content' => $content,
        ]);
    }
}
