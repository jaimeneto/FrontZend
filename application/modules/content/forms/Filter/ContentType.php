<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Form
 * @subpackage Filter
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Content_Form_Filter_ContentType
    extends FrontZend_Module_Form_Filter_Abstract
{
    protected $_cacheAlias = 'ContentType';

    public function initElements()
    {
        $term = $this->addElement('text', 'term', array(
            'label'     => 'Buscar',
            'belongsTo' => 'filter'
        ));
        
        $coreContentTypes = array(
            ''     => 'Todos',
            'null' => 'Tipos principais',
            'Tipos de conteúdo principais' =>
                FrontZend_Container::get('ContentType')->fetchPairs('type', 'id_parent IS NULL', 'type')
        );
        $this->addElement('select', 'id_parent', array(
            'label'        => 'Conteúdo pai',
            'belongsTo'    => 'filter',
            'multiOptions' => $coreContentTypes
        ));

        $this->addElement('select', 'order', array(
            'label'        => 'Ordernar por',
            'belongsTo'    => 'filter',
            'value'        => 'type',
            'multiOptions' => array(
                'Ascendente' => array(
                    'id_content_type'     => 'Id (ascendente)',
                    'type'                => 'Tipo (ascendente)',
                    'plural'              => 'Plural (ascendente)',
                    'id_parent'           => 'Tipo pai (ascendente)'
                ),
                'Descendente' => array(
                    'id_content_type desc' => 'Id (descendente)',
                    'type desc'            => 'Tipo (descendente)',
                    'plural desc'          => 'Plural (descendente)',
                    'id_parent desc'       => 'Tipo pai (descendente)'
                )
            )
        ));
    }

    protected function _initFilters(array $filters)
    {
        $where = array();

        if($filters['term']) {
            $where['type LIKE ?
                    OR plural LIKE ?
                    OR id_content_type LIKE ?'] = "%{$filters['term']}%";
        }
        
        if($filters['id_parent']) {
            if ($filters['id_parent'] == 'null') {
                $where[] = 'id_parent IS NULL';
            } else {
                $where['id_parent = ?'] = $filters['id_parent'];
            }
        }

        return $where;
    }
    
}