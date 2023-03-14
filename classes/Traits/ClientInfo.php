<?php

/**
 * Client info trait.
 *
 * @package     Plugin
 * @subpackage  MpDevTools
 * @author      Murat Purç
 * @copyright   Murat Purç it-solutions
 * @license     GPL-2.0-or-later
 * @link        https://www.purc.de
 */

namespace CONTENIDO\Plugin\MpDevTools\Traits;

use CONTENIDO\Plugin\MpDevTools\Client\Info;

/**
 * Client info trait.
 *
 * Provides functionality to retrieve the client info instances.
 */
trait ClientInfo
{

    /**
     * Client info instances.
     *
     * @var Info
     */
    private $clientInfoInstances = [];

    /**
     * Returns the client info instance.
     *
     * @param int $clientId The client id. If skipped, then the current
     *      client known to the system will be used.
     * @return Info|mixed
     * @throws \cException
     */
    public function getClientInfo(int $clientId = 0)
    {
        if ($clientId === 0) {
            $clientId = \cSecurity::toInteger(\cRegistry::getClientId());
        }

        if (!isset($this->clientInfoInstances[$clientId])) {
            $this->clientInfoInstances[$clientId] = new Info($clientId);
        }

        return $this->clientInfoInstances[$clientId];
    }

}