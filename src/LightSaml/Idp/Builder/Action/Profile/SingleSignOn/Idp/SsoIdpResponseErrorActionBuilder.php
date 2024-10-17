<?php

/*
 * This file is part of the LightSAML-IDP package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the GPL-3 license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Idp\Builder\Action\Profile\SingleSignOn\Idp;

use LightSaml\Action\Profile\Inbound\Message\ResolvePartyEntityIdAction;
use LightSaml\Action\Profile\Outbound\Message\CreateMessageIssuerAction;
use LightSaml\Action\Profile\Outbound\Message\ResolveEndpointSpAcsAction;
use LightSaml\Action\Profile\Outbound\Message\SendMessageAction;
use LightSaml\Idp\Action\Profile\Outbound\Response\CreateResponseAction;
use LightSaml\Idp\Action\Profile\Outbound\StatusResponse\SetStatusAction;
use LightSaml\Builder\Action\Profile\AbstractProfileActionBuilder;
use LightSaml\Model\Protocol\StatusCode;

class SsoIdpResponseErrorActionBuilder extends AbstractProfileActionBuilder
{
    /** @var StatusCode */
    protected $statusCode;

    /**
     * @param string $statusCode
     */
    public function setStatusCode(StatusCode $statusCode)
    {
        $this->statusCode = $statusCode;
    }

    /** @var string */
    protected $statusMessage;

    /**
     * @param string $statusMessage
     */
    public function setStatusMessage($statusMessage)
    {
        $this->statusMessage = $statusMessage;
    }

    /**
     * @return void
     */
    protected function doInitialize()
    {
        $this->add(new ResolvePartyEntityIdAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getPartyContainer()->getSpEntityDescriptorStore(),
            $this->buildContainer->getPartyContainer()->getIdpEntityDescriptorStore(),
            $this->buildContainer->getPartyContainer()->getTrustOptionsStore()
        ), 100);
        $this->add(new CreateResponseAction(
            $this->buildContainer->getSystemContainer()->getLogger()
        ));
        $this->add(new CreateMessageIssuerAction(
            $this->buildContainer->getSystemContainer()->getLogger()
        ));
        $this->add(new SetStatusAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->statusCode,
            $this->statusMessage,
        ));
        $this->add(new ResolveEndpointSpAcsAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getServiceContainer()->getEndpointResolver()
        ));

        // Send
        $this->add(new SendMessageAction(
            $this->buildContainer->getSystemContainer()->getLogger(),
            $this->buildContainer->getServiceContainer()->getBindingFactory()
        ), 400);
    }
}
