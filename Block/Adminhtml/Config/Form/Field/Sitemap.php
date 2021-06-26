<?php
declare(strict_types=1);
namespace Amage\Sitemap\Block\Adminhtml\Config\Form\Field;

class Sitemap extends AbstractFieldArray
{
    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        $this->addColumn('url', ['label' => __('Add Url')]);
        parent::_construct();
    }
}
