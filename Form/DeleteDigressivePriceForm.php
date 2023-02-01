<?php

namespace DigressivePrice\Form;

use DigressivePrice\DigressivePrice;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Validator\Constraints;
use Thelia\Form\BaseForm;
use Symfony\Component\Validator\ExecutionContextInterface;


/**
 * Class DeleteDigressivePriceForm
 * Build form to delete a digressive price
 *
 * @package DigressivePrice\Form
 * @author Etienne PERRIERE <eperriere@openstudio.fr> - Nexxpix - OpenStudio
 */
class DeleteDigressivePriceForm extends BaseForm
{

    protected function buildForm()
    {
        $this->formBuilder
        ->add( "productId", NumberType::class, array(
                "constraints" => array(
                    new Constraints\NotBlank()
                ),
                "label" => $this->translator->trans('product ID', [], DigressivePrice::DOMAIN.'.bo.default')
            )
        )
        ->add( "id", NumberType::class, array(
                "constraints" => array(
                    new Constraints\NotBlank()
                ),
                "label" => 'ID'
            )
        );
    }

    public static function getName()
    {
        return "digressiveprice_delete";
    }
}
