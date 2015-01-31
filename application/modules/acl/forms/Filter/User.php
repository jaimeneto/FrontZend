<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Form
 * @subpackage Filter
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Acl_Form_Filter_User extends FrontZend_Module_Form_Filter_Abstract
{
    public function initElements()
    {
        $this->addElement('text', 'term', array(
            'label'         => 'Buscar',
            'belongsTo'     => 'filter'
        ));

        $this->addElement('select', 'id_role', array(
            'label'         => 'Função',
            'belongsTo'     => 'filter',
            'multiOptions'  => array(
                ''        => 'Todos',
                'Funções' => FrontZend_Container::get('AclRole')->fetchPairs('role')
            )
        ));

        $this->addElement('select', 'status', array(
            'label'         => 'Status',
            'belongsTo'     => 'filter',
            'multiOptions'  => array(
                ''  => 'Todos',
                'A' => 'Ativo',
                'I' => 'Inativo',
                'B' => 'Bloqueado'
            )
        ));

        $this->addElement('select', 'order', array(
            'label'        => 'Ordernar por',
            'belongsTo'    => 'filter',
            'value'        => 'dt_registered desc',
            'multiOptions' => array(
                'Ascendente' => array(
                    'id_user'       => 'Id (ascendente)',
                    'username'      => 'Nome de usuário (ascendente)',
                    'display_name'  => 'Nome de exibição (ascendente)',
                    'name'          => 'Nome (ascendente)',
                    'id_role'       => 'Função (ascendente)',
                    'dt_registered' => 'Data de cadastro (ascendente)',
                    'dt_updated'    => 'Última atualização (ascendente)',
                    'dt_lastaccess' => 'Último acesso (ascendente)'
                ),
                'Descendente' => array(
                    'id_user desc'       => 'Id (descendente)',
                    'username desc'      => 'Nome de usuário (descendente)',
                    'display_name desc'  => 'Nome de exibição (descendente)',
                    'name desc'          => 'Nome (descendente)',
                    'id_role desc'       => 'Função (descendente)',
                    'dt_registered desc' => 'Data de cadastro (descendente)',
                    'dt_updated desc'    => 'Última atualização (descendente)',
                    'dt_lastaccess desc' => 'Último acesso (descendente)'
                )
            )
        ));
    }

    protected function _initFilters(array $filters)
    {
        $where = array();

        if($filters['term']) {
            $where['name LIKE ?
                    OR display_name LIKE ?
                    OR email LIKE ?'] = "%{$filters['term']}%";
        }

        if($filters['id_role']) {
            $where['id_role = ?'] = $filters['id_role'];
        }

        if($filters['status']) {
            $where['status = ?'] = $filters['status'];
        }

        return $where;
    }
   
}