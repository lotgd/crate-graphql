<?php
declare(strict_types=1);

namespace LotGD\Crate\GraphQL\AppBundle\GraphQL\Types;

use Generator;

use LotGD\Core\Game;
use LotGD\ModuleForms\Form;

/**
 * GraphQL Form type.
 */
class FormType extends BaseType
{
    /** @var Form The form entity */
    private $formEntity;

    public function __construct(Game $game, Form $form = null)
    {
        parent::__construct($game);
        $this->formEntity = $form;
    }

    /**
     * Yields the FormElements in this form.
     * @yields FormElementType
     */
    public function getElements(): Generator
    {
        foreach ($this->formEntity->getElements() as $e) {
            yield new FormElementType($this->getGameObject(), $e);
        }
    }

    /**
     * Returns the text for the form submit action.
     * @return string
     */
    public function getSubmitText(): string
    {
        return $this->formEntity->getSubmitText();
    }

    /**
     * Returns the Action to occur on form submit.
     * @return ActionType
     */
    public function getAction(): ActionType
    {
        return new ActionType($this->getGameObject(), $this->formEntity->getAction());
    }

    /**
     * Returns the Attachment type for this Form. Should be the same for all
     * forms.
     * @return string
     */
    public function getType(): string
    {
        return $this->formEntity()->getType();
    }

    /**
     * Returns the id of the Form, from the Attachment interface.
     * @return string
     */
    public function getId(): string
    {
        return $this->formEntity->getId();
    }
}
