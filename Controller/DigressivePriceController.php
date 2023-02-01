<?php

namespace DigressivePrice\Controller;

use DigressivePrice\DigressivePrice;
use Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Core\Security\AccessManager;
use Thelia\Form\Exception\FormValidationException;
use DigressivePrice\Event\DigressivePriceEvent;
use DigressivePrice\Event\DigressivePriceFullEvent;
use DigressivePrice\Event\DigressivePriceIdEvent;
use DigressivePrice\Form\CreateDigressivePriceForm;
use DigressivePrice\Form\UpdateDigressivePriceForm;
use DigressivePrice\Form\DeleteDigressivePriceForm;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

use Thelia\Core\Event\TheliaEvents;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;


/**
 * Class DigressivePriceController
 * Manage actions of DigressivePrice module
 *
 * @package DigressivePrice\Controller
 * @author Etienne PERRIERE <eperriere@openstudio.fr> - Nexxpix - OpenStudio
 */
class DigressivePriceController extends BaseAdminController
{
    public function __construct(EventDispatcherInterface $eventDispatcher)
    {
        $this->eventDispatcher = $eventDispatcher;
    }
    /**
     * @return mixed|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     *
     * @Route("/admin/module/DigressivePrice/create", name="digressive_price_create")
     */
    public function createAction(Request $request, EventDispatcherInterface $eventDispatcher)
    {
        if (null !== $response = $this->checkAuth(AdminResources::MODULE, 'DigressivePrice', AccessManager::CREATE)) {
            return $response;
        }

        // Initialize vars
        $request = $this->getRequest();
        //$cdpf = new CreateDigressivePriceForm($request);
        $cdpf = $this->createForm(CreateDigressivePriceForm::getName());

        try {
            $form = $this->validateForm($cdpf);

            // Dispatch create
            $event = new DigressivePriceEvent(
                $form->get('productId')->getData(),
                $form->get('price')->getData(),
                $form->get('promo')->getData(),
                $form->get('quantityFrom')->getData(),
                $form->get('quantityTo')->getData()
            );
            //$this->dispatch('action.createDigressivePrice', $event);
            $this->eventDispatcher->dispatch($event, 'action.createDigressivePrice');


        } /*catch (\Exception $ex) {
            $this->setupFormErrorContext(
                //Translator::getInstance()->trans(
                $this->getTranslator()->trans("Failed to create price slice", [], DigressivePrice::DOMAIN),
                $this->createStandardFormValidationErrorMessage($ex),
                $cdpf,
                $ex
            );
        }*/
        catch (FormValidationException $ex) {
            $this->setupFormErrorContext(
                $this->getTranslator()->trans("Failed to create price slice", [], DigressivePrice::DOMAIN),
                $this->createStandardFormValidationErrorMessage($ex),
                $cdpf,
                $ex
            );
        }

        return $this->generateRedirectFromRoute(
            'admin.products.update',
            array(
                'product_id' => $this->getRequest()->get('product_id'),
                //'product_id' => $request->getCurrentRequest()->request->get('product_id'),
                'current_tab' => 'digressive-prices'
            )
        );
    }

    /**
     * @return mixed|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     *
     * @Route("/admin/module/DigressivePrice/update", name="digressive_price_update")
     */
    public function updateAction(EventDispatcherInterface $eventDispatcher)
    {
        if (null !== $response = $this->checkAuth(AdminResources::MODULE, 'DigressivePrice', AccessManager::UPDATE)) {
            return $response;
        }

        // Initialize vars
        //$request = $this->getRequest();
        $request = $requestStack->getCurrentRequest();
        //$udpf = new UpdateDigressivePriceForm($request);
        $udpf = $this->createForm(UpdateDigressivePriceForm::getName());


        try {
            $form = $this->validateForm($udpf);

            // Dispatch update
            $event = new DigressivePriceFullEvent(
                $form->get('id')->getData(),
                $form->get('productId')->getData(),
                $form->get('price')->getData(),
                $form->get('promo')->getData(),
                $form->get('quantityFrom')->getData(),
                $form->get('quantityTo')->getData()
            );

            //$this->dispatch('action.updateDigressivePrice', $event);
            $this->eventDispatcher->dispatch($event, 'action.updateDigressivePrice');
        //} catch (\Exception $ex) {
        } catch (FormValidationException $ex) {

            $this->setupFormErrorContext(
                $this->getTranslator()->trans("Failed to update price slice", [], DigressivePrice::DOMAIN),
                $this->createStandardFormValidationErrorMessage($ex),
                $udpf,
                $ex
            );
        }

        return $this->generateRedirectFromRoute(
            'admin.products.update',
            array(
                //'product_id' => $this->getRequest()->get('product_id'),
                'product_id' => $requestStack->getCurrentRequest()->request->get('product_id'),

                'current_tab' => 'digressive-prices'
            )
        );
    }

    /**
     * @return mixed|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     *
     * @Route("/admin/module/DigressivePrice/delete", name="digressive_price_delete")
     */
    public function deleteAction(RequestStack $requestStack, EventDispatcherInterface $eventDispatcher)
    {
        if (null !== $response = $this->checkAuth(AdminResources::MODULE, 'DigressivePrice', AccessManager::DELETE)) {
            return $response;
        }

        // Initialize vars
        //$request = $this->getRequest();
        $request = $requestStack->getCurrentRequest();

        //$ddpf = new DeleteDigressivePriceForm($request);
        $ddpf = $this->createForm(DeleteDigressivePriceForm::getName());


        try {
            $form = $this->validateForm($ddpf);

            // Dispatch delete
            $event = new DigressivePriceIdEvent($form->get('id')->getData());
            //$this->dispatch('action.deleteDigressivePrice', $event);
            $this->eventDispatcher->dispatch($event, 'action.deleteDigressivePrice');

        //} catch (\Exception $ex) {
        } catch (FormValidationException $ex) {

            $this->setupFormErrorContext(
                $this->getTranslator()->trans("Failed to delete price slice", [], DigressivePrice::DOMAIN),
                $ex->getMessage(),
                $ddpf,
                $ex
            );
        }

        return $this->generateRedirectFromRoute(
            'admin.products.update',
            array(
                //'product_id' => $this->getRequest()->get('product_id'),
                'product_id' => $requestStack->getCurrentRequest()->request->get('product_id'),
                'current_tab' => 'digressive-prices'
            )
        );
    }
}
