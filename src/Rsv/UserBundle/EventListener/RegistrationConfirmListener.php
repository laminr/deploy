<?php
namespace Rsv\UserBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Rsv\DeployBundle\Entity\UserLogin;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Doctrine\UserManager;
use FOS\UserBundle\Event\FilterUserResponseEvent;

class RegistrationConfirmListener implements EventSubscriberInterface
{

    protected $um;
    protected $em;

    public function __construct(UserManager $um, $em)
    {
        $this->um = $um;
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::REGISTRATION_COMPLETED => 'onRegistrationCompleted'
        );
    }

    public function onRegistrationCompleted(FilterUserResponseEvent $event)
    {
        $user = $event->getUser();
        $user->setEnabled(false);

        $this->um->updateUser($user);
        $this->em->flush();

    }
}