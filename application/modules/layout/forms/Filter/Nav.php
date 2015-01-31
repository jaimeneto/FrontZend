<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Form
 * @subpackage Filter
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Layout_Form_Filter_Nav extends FrontZend_Module_Form_Filter_Abstract
{
    public function initElements()
    {
        $term = $this->addElement('text', 'term', array(
            'label' => 'Buscar',
            'belongsTo' => 'filter'
        ));

        $this->addElement('select', 'order', array(
            'label'        => 'Ordernar por',
            'belongsTo'    => 'filter',
            'value'        => 'order',
            'multiOptions' => array(
                'Ascendente' => array(
                    'id_layout_nav'   => 'Id (ascendente)',
                    'name'            => 'Nome (ascendente)',
                    'order'           => 'Ordem (ascendente)'
                ),
                'Descendente' => array(
                    'id_layout_nav desc'   => 'Id (descendente)',
                    'name desc'            => 'Nome (descendente)',
                    'order desc'           => 'Ordem (descendente)'
                )
            )
        ));
    }

    protected function _initFilters(array $filters)
    {
        $where = array();

        if($filters['term']) {
            $where['name LIKE ?
                    OR uri LIKE ?
                    OR resource LIKE ?'] = "%{$filters['term']}%";
        }

        return $where;
    }
    
}