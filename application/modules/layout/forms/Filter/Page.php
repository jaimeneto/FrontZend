<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Form
 * @subpackage Filter
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Layout_Form_Filter_Page extends FrontZend_Module_Form_Filter_Abstract
{
    public function initElements()
    {
        $term = $this->addElement('text', 'term', array(
            'label' => 'Buscar',
            'belongsTo' => 'filter'
        ));

        $themes = FrontZend_Container::get('LayoutTheme')->findAll(array(
            'where' => array('env != ?' => 'backend'),
            'order' => 'theme'
        ));
        $multiOptions = array('' => 'Todos');
        foreach($themes as $theme) {
            $multiOptions[$theme->env][$theme->id] = (string) $theme;
        }
        $this->addElement('select', 'id_layout_theme', array(
            'label'        => 'Tema',
            'belongsTo'    => 'filter',
            'multiOptions' => $multiOptions
        ));

        $coreContentTypes = array(
            ''     => 'Todos',
            'Tipos de conteúdo' =>
                FrontZend_Container::get('ContentType')->fetchPairs('type')
        );
        $this->addElement('select', 'id_content_type', array(
            'label'        => 'Tipo de conteúdo',
            'belongsTo'    => 'filter',
            'multiOptions' => $coreContentTypes
        ));

        $this->addElement('select', 'order', array(
            'label'        => 'Ordernar por',
            'belongsTo'    => 'filter',
            'value'        => 'page',
            'multiOptions' => array(
                'Ascendente' => array(
                    'id_layout_page'       => 'Id (ascendente)',
                    'page'                 => 'Página (ascendente)',
                    'id_content_type'      => 'Tipo (ascendente)',
                    'id_content'           => 'Conteúdo (ascendente)'
                ),
                'Descendente' => array(
                    'id_layout_page desc'  => 'Id (descendente)',
                    'page desc'            => 'Página (descendente)',
                    'id_content_type desc' => 'Tipo (descendente)',
                    'id_content desc'      => 'Conteúdo (descendente)'
                )
            )
        ));
    }

    protected function _initFilters(array $filters)
    {
        $where = array();

        if($filters['term']) {
            $where['page LIKE ?
                    OR special LIKE ?'] = "%{$filters['term']}%";
        }
        if($filters['id_layout_theme']) {
            $where['id_layout_theme = ?'] = $filters['id_layout_theme'];
        }
        if($filters['id_content_type']) {
            $where['id_content_type = ?'] = $filters['id_content_type'];
        }

        return $where;
    }
    
}