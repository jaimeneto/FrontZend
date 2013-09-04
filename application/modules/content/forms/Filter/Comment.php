<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Form
 * @subpackage Filter
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Content_Form_Filter_Comment extends FrontZend_Module_Form_Filter_Abstract
{
    protected $_cacheAlias = 'Comment';

    public function initElements()
    {        
        $this->addElement('text', 'term', array(
            'label'     => 'Buscar',
            'class'     => 'input-block-level',
            'belongsTo' => 'filter'
        ));
        
        $contentTypes = array(
            '' => 'Todos',
            'Tipos de conteúdo' => FrontZend_Container::get('ContentType')->fetchPairs('type')
        );
        $this->addElement('select', 'id_content_type', array(
            'label'        => 'Tipo de conteúdo',
            'class'        => 'input-block-level',
            'belongsTo'    => 'filter',
            'multiOptions' => $contentTypes
        ));

        $this->addElement('select', 'status', array(
            'label'     => 'Status',
            'class'     => 'input-block-level',
            'belongsTo' => 'filter',
            'multiOptions' => array(
                ''  => 'Todos',
                'A' => 'Ativo',
                'I' => 'Inativo',
                'D' => 'Excluído',
            )
        ));

        $this->addElement('select', 'order', array(
            'label'     => 'Ordenar por',
            'class'     => 'input-block-level',
            'belongsTo' => 'filter',
            'value'     => 'dt_updated desc',
            'multiOptions' => array(
                'Ascendente' => array(
                    'id_comment'           => 'Id (ascendente)',
                    'name'                 => 'Nome (ascendente)',
                    'id_content_type'      => 'Tipo (ascendente)',
                    'id_content'           => 'Conteúdo (ascendente)',
                    'status'               => 'Status (ascendente)',
                    'dt_updated'           => 'Criação/Alteração (ascendente)'
                ),
                'Descendente' => array(
                    'id_comment desc'      => 'Id (ascendente)',
                    'name desc'            => 'Nome (ascendente)',
                    'id_content_type desc' => 'Tipo (ascendente)',
                    'id_content desc'      => 'Conteúdo (descendente)',
                    'status desc'          => 'Status (ascendente)',
                    'dt_updated desc'      => 'Criação/Alteração (ascendente)'
                )
            )
        ));
    }

    protected function _initFilters(array $filters)
    {
        $where = array();

        if($filters['term']) {
            $where['title LIKE ?
                    OR slug LIKE ?'] = "%{$filters['term']}%";
        }

        if($filters['id_content_type']) {
            $where['id_content_type = ?'] = $filters['id_content_type'];
        }

        if($filters['status']) {
            $where['status = ?'] = $filters['status'];
        }

        return $where;
    }

}