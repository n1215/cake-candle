<?php

namespace N1215\CakeCandle\Http;

use Cake\Controller\Controller;
use Cake\Controller\Exception\MissingActionException;
use Cake\Http\ServerRequest;
use LogicException;
use N1215\CakeCandle\ContainerBagLocator;

trait AssistedAction
{
    /**
     * @param string
     * @return bool
     * @throws \ReflectionException
     */
    abstract public function isAction($action);

    /**
     * Dispatches the controller action. Checks that the action
     * exists and isn't private.
     *
     * @return mixed The resulting response.
     * @throws \ReflectionException
     * @see Controller::invokeAction()
     */
    public function invokeAction()
    {
        /** @var ServerRequest|null $request */
        $request = $this->request;
        if (!$request) {
            throw new LogicException('No Request object configured. Cannot invoke action');
        }
        if (!$this->isAction($request->getParam('action'))) {
            throw new MissingActionException([
                'controller' => $this->name . 'Controller',
                'action' => $request->getParam('action'),
                'prefix' => $request->getParam('prefix') ?: '',
                'plugin' => $request->getParam('plugin'),
            ]);
        }
        /* @var callable $callable */
        $callable = [$this, $request->getParam('action')];

        return ContainerBagLocator::get()->call(
            $callable,
            $request->getParam('pass')
        );
    }
}
