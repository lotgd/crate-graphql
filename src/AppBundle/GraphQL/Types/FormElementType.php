<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Types;

use Generator;

use LotGD\Core\Game;
use LotGD\ModuleForms\FormElement;

/**
 * GraphQL Form element type.
 */
class FormElementType extends BaseType
{
    /** @var FormElement The element entity */
    private $formElementEntity;

    public function __construct(Game $game, FormElement $formElement = null)
    {
        parent::__construct($game);
        $this->formElementEntity = $formElement;
    }

    /**
     * A non-unique identifier for this form element.
     * @return string
     */
    public function getKey(): string
    {
        return $this->formElementEntity->getKey();
    }

    /**
     * The type of this form element.
     * @return FormElementTypeType
     */
    public function getType(): FormElementTypeType
    {
        switch ($this->formElementEntity->getType()) {
            case FormElementType::Input:
                return FormElementTypeType::INPUT;
            case FormElementType::Button:
                return FormElementTypeType::BUTTON
            default:
                // TODO: we should log this.
                return -1;
        }
    }

    /**
     * The label shown to this user.
     * @return string
     */
    public function getLabel(): string
    {
        return $this->formElementEntity->getLabel();
    }

    /**
     * The value, possibly including a default value, of this element, encoded
     * as a JSON string.
     * @return string
     */
    public function getValue(): string
    {
        $s = json_encode($this->formElementEntity->getValue());
        if ($s) {
            return $s;
        } else {
            // TODO: log this.
            return "";
        }
    }

    /**
     * Options applied to this form element.
     * @return array
     */
    public function getOptions(): array
    {
        $ret = [];
        $options = $this->formElementEntity->getOptions();
        if ($options->get(FormElementOptions::Disabled)) {
            array_push(new FormElementOptionType::DISABLED);
        }
        return $ret;
    }
}
