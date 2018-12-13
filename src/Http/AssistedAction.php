<?php

namespace N1215\CakeCandle\Http;

use Cake\Controller\Exception\MissingActionException;
use Cake\Http\ServerRequest;
use N1215\CakeCandle\Container\ContainerBag;

/**
 * trait AssistedAction
 * @package N1215\CakeCandle\Http
 */
trait AssistedAction
{
    /**
     * Dispatches the controller action. Checks that the action
     * exists and isn't private.
     *
     * @return mixed The resulting response.
     * @throws \ReflectionException
     */
    public function invokeAction()
    {
        /** @var ServerRequest $request */
        $request = $this->request;
        if (!$request) {
            throw new \LogicException('No Request object configured. Cannot invoke action');
        }
        if (!$this->isAction($request->getParam('action'))) {
            throw new MissingActionException([
                'controller' => $this->name . 'Controller',
                'action' => $request->getParam('action'),
                'prefix' => $request->getParam('prefix') ?: '',
                'plugin' => $request->getParam('plugin'),
            ]);
        }

        return ContainerBag::getInstance()->invoke(
            $this,
            $request->getParam('action'),
            $request->getParam('pass')
        );
    }
}
