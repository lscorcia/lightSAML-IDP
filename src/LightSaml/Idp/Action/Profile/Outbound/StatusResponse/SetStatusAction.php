<?php

/*
 * This file is part of the LightSAML-IDP package.
 *
 * (c) Milos Tomic <tmilos@lightsaml.com>
 *
 * This source file is subject to the GPL-3 license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LightSaml\Idp\Action\Profile\Outbound\StatusResponse;

use LightSaml\Action\Profile\AbstractProfileAction;
use LightSaml\Context\Profile\Helper\MessageContextHelper;
use LightSaml\Context\Profile\ProfileContext;
use LightSaml\Model\Protocol\Status;
use LightSaml\Model\Protocol\StatusCode;
use LightSaml\SamlConstants;
use Psr\Log\LoggerInterface;

class SetStatusAction extends AbstractProfileAction
{
    /** @var StatusCode */
    protected $statusCode;

    /** @var string */
    protected $statusMessage;

    /**
     * @param LoggerInterface $logger
     * @param string          $statusCode
     * @param string          $statusMessage
     */
    public function __construct(LoggerInterface $logger, StatusCode $statusCode = new StatusCode(SamlConstants::STATUS_SUCCESS), $statusMessage = null)
    {
        parent::__construct($logger);

        $this->statusCode = $statusCode;
        $this->statusMessage = $statusMessage;
    }

    /**
     * @param ProfileContext $context
     */
    protected function doExecute(ProfileContext $context)
    {
        $statusResponse = MessageContextHelper::asStatusResponse($context->getOutboundContext());

        $statusResponse->setStatus(new Status(new StatusCode($this->statusCode), $this->statusMessage));
    }
}
