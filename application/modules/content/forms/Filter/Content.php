<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Form
 * @subpackage Filter
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Content_Form_Filter_Content extends FrontZend_Module_Form_Filter_Abstract
{
    protected $_cacheAlias = 'Content';

    public function initElements()
    {
        $this->addElement('text', 'term', array(
            'label'     => 'Buscar',
            'class'     => 'input-block-level',
            'belongsTo' => 'filter'
        ));
        
        $options = array('order' => 'type');
        $multiOptions = array(
            ''  => 'Todos',
            'Tipos de conteúdo' => FrontZend_Container::get('ContentType')
                ->fetchPairs('type', array('id_parent IS NOT NULL '
                    . "OR id_content_type IN ('section', 'link', 'forum')"
                ))
        );
        $this->addElement('select', 'id_content_type', array(
            'label'        => 'Tipo de conteúdo',
            'belongsTo'    => 'filter',
            'class'        => 'input-block-level',
            'multiOptions' => $multiOptions
        ));

        $this->addElement('select', 'status', array(
            'label'        => 'Status',
            'belongsTo'    => 'filter',
            'class'        => 'input-block-level',
            'multiOptions' => array(
                ''  => 'Todos',
                'A' => 'Ativo',
                'I' => 'Inativo',
                'L' => 'Trancado',
                'D' => 'Excluído',
            )
        ));

        $this->addElement('select', 'order', array(
            'label'        => 'Ordenar por',
            'class'        => 'input-block-level',
            'multiOptions' => array(
                'Ascendente' => array(
                    'id_content'           => 'Id (ascendente)',
                    'title'                => 'Título (ascendente)',
                    'slug'                 => 'Slug (ascendente)',
                    'id_content_type'      => 'Tipo (ascendente)',
                    'status'               => 'Status (ascendente)',
                    'dt_published'         => 'Publicação (ascendente)'
                ),
                'Descendente' => array(
                    'id_content desc'      => 'Id (descendente)',
                    'title desc'           => 'Título (descendente)',
                    'slug desc'            => 'Slug (descendente)',
                    'id_content_type desc' => 'Tipo (descendente)',
                    'status desc'          => 'Status (descendente)',
                    'dt_published desc'    => 'Publicação (descendente)'
                )
            ),
            'value'        => 'dt_published desc',
            'belongsTo'    => 'filter',
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