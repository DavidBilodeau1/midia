<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Symfony\Component\HttpKernel\Event\TerminateEvent;

class ActivitySubscriber implements EventSubscriberInterface {

    private $em;
    private $security;
    private $authorizationChecker;

    public function __construct(
    EntityManagerInterface $em, AuthorizationCheckerInterface $authorizationChecker, Security $security) {
        $this->em = $em;
        $this->security = $security;
        $this->authorizationChecker = $authorizationChecker;
    }

    public function onController(ControllerEvent $event) {
        $user = $this->security->getUser();
            if ($user && ($this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY') || $this->authorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED'))) {
                if (!$user->isActiveNow()) {
                    $user->setLastActivityAt(new \DateTime());
                    $this->em->persist($user);
                    $this->em->flush($user);
                }
            }
    }

    public function onTerminate(TerminateEvent $event){
        $request = $event->getRequest();
        $user = $this->security->getUser();
        if ($user && ($this->authorizationChecker->isGranted('IS_AUTHENTICATED_FULLY') || $this->authorizationChecker->isGranted('IS_AUTHENTICATED_REMEMBERED'))) {
            $user->setCurrentPath($request->getPathInfo());
            $this->em->persist($user);
            $this->em->flush($user);
        }
    }

    public static function getSubscribedEvents() {
        return [
            // must be registered before (i.e. with a higher priority than) the default Locale listener
            KernelEvents::CONTROLLER => [['onController', 20]],
            KernelEvents::TERMINATE => [['onTerminate', 20]]
        ];
    }

}
