<?php
namespace Boekkooi\Bundle\JqueryValidationBundle\Form;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;

/**
 * @author Warnar Boekkooi <warnar@boekkooi.net>
 */
class FormRuleCollection implements \IteratorAggregate, \Countable
{
    /**
     * @var RuleCollection[]
     */
    private $rules = array();

    /**
     * @var FormInterface
     */
    private $form;

    /**
     * @var FormView
     */
    private $view;

    /**
     * @var FormRuleCollection|null
     */
    private $root;

    public function __construct(FormInterface $form, FormView $view, FormRuleCollection $root = null)
    {
        $this->form = $form;
        $this->view = $view;
        $this->root = $root;
    }

    /**
     * @return FormInterface
     */
    public function getForm()
    {
        return $this->form;
    }

    /**
     * @return FormView
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @return bool
     */
    public function isRoot()
    {
        return $this->root === null;
    }

    /**
     * @return FormRuleCollection|null
     */
    public function getRoot()
    {
        return $this->root;
    }

    /**
     * {@inheritdoc}
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->rules);
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return count($this->rules);
    }

    /**
     * Set a rule collection.
     *
     * @param string|FormView $form The form full_name or view instance
     * @param RuleCollection $collection A RuleCollection instance
     */
    public function set($form, RuleCollection $collection)
    {
        $name = static::getFormName($form);

        unset($this->rules[$name]);

        $this->add($name, $collection);
    }

    /**
     * Adds a rule collection.
     *
     * @param string|FormView $form The form full_name or view instance
     * @param RuleCollection $collection A RuleCollection instance
     */
    public function add($form, RuleCollection $collection)
    {
        $name = static::getFormName($form);
        if (isset($this->rules[$name])) {
            $this->rules[$name]->addCollection($collection);
        } else {
            $this->rules[$name] = $collection;
        }
    }

    /**
     * Returns all rules in this collection.
     *
     * @return RuleCollection[] An array of rules
     */
    public function all()
    {
        return $this->rules;
    }

    /**
     * Gets a rule list by name.
     *
     * @param string|FormView $form The form full_name or view instance
     * @return RuleCollection|null A array of Rule instances or null when not found
     */
    public function get($form)
    {
        $name = static::getFormName($form);
        return isset($this->rules[$name]) ? $this->rules[$name] : null;
    }

    /**
     * Removes a rule or an array of rules by name from the collection
     *
     * @param string|FormView $form The form full_name or view instance
     */
    public function remove($form)
    {
        $name = static::getFormName($form);
        unset($this->rules[$name]);
    }

    /**
     * Adds a form rule collection at the end of the current set by appending all
     * form rules of the added collection.
     *
     * @param FormRuleCollection $collection A FormRuleCollection instance
     */
    public function addCollection(FormRuleCollection $collection)
    {
        foreach ($collection->all() as $name => $rule) {
            if (isset($this->rules[$name])) {
                $this->rules[$name]->addCollection($rule);
            } else {
                $this->rules[$name] = $rule;
            }
        }
    }

    public static function getFormName($form)
    {
        if ($form instanceof FormView && isset($form->vars['full_name'])) {
            return $form->vars['full_name'];
        }
        if (is_string($form)) {
            return $form;
        }

        // TODO use bundle exception
        throw new \InvalidArgumentException();
    }
} 