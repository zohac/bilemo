<?php

namespace AppBundle\EventListener;

use AppBundle\Service\CacheClearerService;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class CacheClearerListener
{
    /**
     * The CacheClearerService class.
     *
     * @var CacheClearerService
     */
    private $cacheClearerService;

    /**
     * All roads whose cache must be cleared.
     *
     * @var array
     */
    private $routeToClear = [
        'users_delete',
        'users_update',
    ];

    /**
     * Constructor.
     *
     * @param CacheClearerService $cacheClearerService
     */
    public function __construct(CacheClearerService $cacheClearerService)
    {
        $this->cacheClearerService = $cacheClearerService;
    }

    /**
     * Clear the cache if the route matches.
     *
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        // Clear the cache if the route matches
        if (in_array($event->getRequest()->get('_route'), $this->routeToClear)) {
            $this->cacheClearerService->clear();
        }

        return;
    }
}
