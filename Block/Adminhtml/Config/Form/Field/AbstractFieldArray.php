<?php
declare(strict_types=1);
namespace Amage\Sitemap\Block\Adminhtml\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray as FormAbstractFieldArray;
use Magento\Framework\Data\Form\Element\AbstractElement;

class AbstractFieldArray extends FormAbstractFieldArray
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->setTemplate('config/form/field/array.phtml');

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add');

        parent::_construct();
    }

    /**
     * {@inheritdoc}
     */
    public function render(AbstractElement $element)
    {
        $this->setElement($element);

        return $this->_toHtml();
    }
}
