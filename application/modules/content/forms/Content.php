<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Form
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Content_Form_Content extends Bootstrap_Form_Horizontal
{
    protected $_contentType = null;
    protected $_metafields = array();

    public function __construct($options = null)
    {
        $this->setAttrib('id', strtolower(__CLASS__));

        $this->addPrefixPath('FrontZend_Form', 'FrontZend/Form');
        $this->addPrefixPath('Bootstrap_Form', 'Bootstrap/Form');

        parent::__construct($options);

        if (isset($options['contentType']) && $options['contentType']) {
            $this->_contentType = $options['contentType'];
            unset($options['contentType']);
        }
        
        $this->initElements();
        $this->initInfo();
        $this->initButtons();
    }

    public function initElements()
    {
        $tbContentType = FrontZend_Container::get('ContentType');
        $contentTypes = array(
            ''                  => 'Selecione um tipo de conteúdo',
            'Tipos de conteúdo' => $tbContentType->fetchPairs('type', array(
                'id_parent IS NOT NULL OR id_content_type IN ' .
                "('section', 'link', 'forum')"
            ))
        );
        $this->addElement('select', 'id_content_type', array(
            'label'        => 'Tipo de conteúdo',
            'multiOptions' => $contentTypes,
        ));
        $groupElements[] = 'id_content_type';

        $this->addElement('text', 'title', array(
            'label' => 'Título',
        ));
        $groupElements[] = 'title';

        $this->addElement('text', 'slug', array(
            'label' => 'Slug',
            'prepend' => SITE_URL . '/'
        ));
        $groupElements[] = 'slug';

        $this->addElement('selectDatetime', 'dt_published', array(
            'label'  => 'Data de publicação',
            'format' => 'yyyy-MM-dd HH:mm:ss',
            'value'  => Zend_Date::now(),
        ));
        $groupElements[] = 'dt_published';

        $this->addElement('textarea', 'text', array(
            'label' => 'Texto',
            'rows'  => 15
        ));
        $groupElements[] = 'text';

        $this->addElement('textarea', 'excerpt', array(
            'label' => 'Resumo',
            'rows'  => 4
        ));
        $groupElements[] = 'excerpt';

        $this->addElement('hidden', 'id_content');
        $groupElements[] = 'id_content';

        $groupElements = $this->initMetafieldElements($groupElements);
        
        $this->addElement('text', 'keywords', array(
            'label'   => 'Palavras-chave',
            'prepend' => '<span class="glyphicon glyphicon-tags"></span>'
        ));
        $groupElements[] = 'keywords';

        $this->addElement('radio', 'status', array(
            'label'        => 'Status',
            'inline'       => true,
            'value'        => 'I',
            'separator'    => '',
            'multiOptions' => array(
                'A' => 'Ativo',
                'I' => 'Inativo',
//                'L' => 'Trancado',
//                'D' => 'Excluído',
            ),
        ));
        $groupElements[] = 'status';

        $this->addDisplayGroup(
            $groupElements, 'main_elements', array(
                'legend'     => 'Dados gerais',
                'decorators' => array(
                    'FormElements', array(
                        'HtmlTag', array(
                            'tag'   => 'div',
                            'role'  => 'tabpanel',
                            'class' => 'tab-pane active form-horizontal',
                            'id'    => 'main_elements'
                        )
                    )
                )
            )
        );
    }

    public function initMetafieldElements($groupElements)
    {
        if (!$this->_contentType) {
            return;
        }
        
        $metaTypes = array(
            'field'        => array(
                'field'     => 'type',
                'displayId' => 'meta_elements',
                'legend'    => 'Dados extra'
            ),
            'relationship' => array(
                'field'     => 'display',
                'displayId' => 'relationship_elements',
                'legend'    => 'Relacionamentos'
            ),
            'file'         => array(
                'field'     => 'type',
                'displayId' => 'file_elements',
                'legend'    => 'Arquivos'
            )
        );

        foreach($metaTypes as $datatype => $options) {
            $metafields = FrontZend_Container::get('Metafield')->findAll(
                array(
                    'where' => array(
                        'id_content_type = ?' => $this->_contentType,
                        'datatype = ?'        => $datatype
                    ),
                    'order' => 'order'
                )
            );

            if ($metafields) {
                foreach($metafields as $metafield) {
                    $metaOptions = $metafield->getOptions();
                    $metaOptions['belongsTo'] = 'meta';
                    $metaElement = 'Content_Form_Meta_' . ucfirst($datatype)
                        . '_' . ucfirst($metaOptions[$options['field']]);
                    $element = new $metaElement($metafield->fieldname,
                        $metaOptions);
                    $element->setDecorators($this->_elementDecorators);
                    $this->addElement($element);
                    $this->_metafields[$metafield->fieldname] = $metafield;
                    $groupElements[] = $metafield->fieldname;
                }
            }
        }
        return $groupElements;
    }

    public function initInfo()
    {
        $this->addElement('StaticText', 'creator_name', array(
            'label'    => 'Cadastrado por',
            'class'    => 'form-control',
            'disabled' => true,
            'ignore'   => true
        ));
        $groupElements[] = 'creator_name';

        $this->addElement('StaticText', 'date_created', array(
            'label'  => 'Data da criação',
            'class'  => 'form-control',
            'disabled' => true,
            'ignore' => true
        ));
        $groupElements[] = 'date_created';

        $this->addElement('StaticText', 'date_updated', array(
            'label'  => 'Última atualização',
            'class'  => 'form-control',
            'disabled' => true,
            'ignore' => true
        ));
        $groupElements[] = 'date_updated';

        $this->addElement('StaticText', 'count_comments', array(
            'label'  => 'Comentários',
            'class'  => 'form-control',
            'disabled' => true,
            'ignore' => true
        ));
        $groupElements[] = 'count_comments';

        $this->addDisplayGroup(
            $groupElements, 'content_info', array(
                'legend'     => 'Informações',
                'decorators' => array(
                    'FormElements',
                    array('HtmlTag', array(
                            'tag'   => 'div',
                            'role'  => 'tabpanel',
                            'class' => 'tab-pane form-horizontal',
                            'id'    => 'content_info'
                        ))
                )
            )
        );
    }

    public function initButtons()
    {
        $this->addElement('submit', 'save', array(
            'label'         => 'Salvar',
            'ignore'        => true,
            'buttonType'    => Bootstrap_Form_Element_Submit::BUTTON_PRIMARY,
            'size'          => Bootstrap_Form_Element_Submit::BUTTON_SIZE_LARGE,
        ));

        $this->addElement('submit', 'apply', array(
            'label'         => 'Aplicar',
            'ignore'        => true,
            'buttonType'    => Bootstrap_Form_Element_Submit::BUTTON_SUCCESS,
            'size'          => Bootstrap_Form_Element_Submit::BUTTON_SIZE_LARGE,
        ));

        $this->addElement('submit', 'cancel', array(
            'label'         => 'Cancelar',
            'buttonType'    => Bootstrap_Form_Element_Submit::BUTTON_DEFAULT,
            'size'          => Bootstrap_Form_Element_Submit::BUTTON_SIZE_LARGE,
            'ignore'        => true
        ));

        $this->addDisplayGroup(array('save', 'apply', 'cancel'), 'buttons', array(
            'decorators' => array(
                'FormElements', 
                array('HtmlTag', array(
                    'class' => 'col-sm-offset-2', 
                    'tag'   => 'div',
                    'style' => 'clear:both'
                ))
            ),
        ));
    }

    public function init()
    {
        $this->setAttrib('class', 'tab-content');

        $model = new Content_Model_Content();
        $if = $model->getInputFilter();
        foreach ($if as $name => $options) {
            $element = $this->getElement($name);
            if ($element) {
                $element->setOptions($options);
            }
        }
    }

    public function populate(array $values)
    {
        if (isset($values['id_content'])) {
            $content = FrontZend_Container::get('Content')->findById($values['id_content']);

            $this->removeElement('id_content_type');
            $this->addElement('hidden', 'id_content_type', array(
                'value' => $content->id_content_type
            ));

            $values['creator_name'] = $content->getUser()->name;
            $values['date_created'] = ucfirst($content->getDateCreated()
                ->get("EEEE, dd 'de' MMMM 'de' yyyy 'às' HH:mm"));
            $values['date_updated'] = ucfirst($content->getDateUpdated()
                ->get("EEEE, dd 'de' MMMM 'de' yyyy 'às' HH:mm"));
            $values['count_comments'] = $content->countComments();
        }

        return parent::populate($values);
    }

    public function persistData()
    {
        $values = $this->getValues();

        if ($values) {
            $values['dt_updated'] =
                Zend_Date::now()->get('yyyy-MM-dd HH:mm:ss');
            if (isset($values['id_content'])) {
                $content = FrontZend_Container::get('Content')->findById(
                    $values['id_content']);
                $oldSlug = $content->slug;
            } else {
                $content = FrontZend_Container::get('Content')->createRow();
                $values['id_user'] = 
                    Zend_Auth::getInstance()->getIdentity()->id_user;
                $values['dt_created'] = $values['dt_updated'];
                if (!isset($values['dt_published'])) {
                    $values['dt_published'] = $values['dt_created'];
                }
                if (!isset($values['fixed'])) {
                    $values['fixed'] = 0;
                }
            }
//            if (isset($values['dt_published'])) {
//                $dt_published = new Zend_Date($values['dt_published'],
//                    $this->getElement('dt_published')->getFormat());
//                $values['dt_published'] = $dt_published->get('yyyy-MM-dd HH:mm:ss');
//            }

            $data = $values; unset($data['meta']);
            $content->setFromArray($data);

            $saves = 0;

            $result = FrontZend_Container::get('Content')->save($content);

            if (!$content->id) {
                return $result;
            }

            if ($result) {
                $saves++;
            }
            
            if ($content->id && $this->_metafields) {
                foreach($this->_metafields as $fieldname => $metafield) {
                    switch($metafield->datatype) {
                    case 'field':
                        $contentMetafield = $content->getMetafield($fieldname);
                        $tbContentMetafield = FrontZend_Container::get('ContentMetafield');
                        if (!$contentMetafield) {
                            $contentMetafield = $tbContentMetafield->createRow();
                            $contentMetafield->id_metafield = $metafield->id;
                            $contentMetafield->id_content = $content->id;
                        } elseif (!isset($values['meta'][$fieldname])
                            && $contentMetafield->value) {
                                if ($tbContentMetafield->deleteById($contentMetafield->id)) {
                                    $saves++;
                                }
                            break;
                        }
                        
                        if ($values['meta'][$fieldname]) {
                            $contentMetafield->value = is_array($values['meta'][$fieldname])
                                    ? implode(',',$values['meta'][$fieldname])
                                    : $values['meta'][$fieldname];
                            if ($tbContentMetafield->save($contentMetafield)) {
                                $saves++;
                            }
                        }
                        break;

                    case 'relationship':
                        if (!isset($values['meta'][$fieldname])) {
                            $values['meta'][$fieldname] = array();
                        }
                        
                        switch($metafield->getOption('type')) {
                        case 'users':
                            $contentUsers = $content->getUsers($fieldname);
                            $currContentUsers = array();
                            foreach($contentUsers as $contentUser) {
                                $currContentUsers[$contentUser->id_content_user] =
                                    $contentUser->id_user;
                            }

                            $insert = array_diff(
                                    (array) $values['meta'][$fieldname],
                                    $currContentUsers
                                );

                            $delete = array_diff(
                                    $currContentUsers,
                                    (array) $values['meta'][$fieldname]
                                );

//                                pr(array(
//                                    'current' => $currContentUsers,
//                                    'values'  => (array) $values['meta'][$fieldname],
//                                    'insert'  => $insert,
//                                    'delete'  => $delete),1);

                            $tbContentUser = FrontZend_Container::get('ContentUser');

                            if ($insert) foreach($insert as $id_user) {
                                if ($tbContentUser->insert(array(
                                        'id_content' => $content->id,
                                        'id_user'    => $id_user,
                                        'rel_type'   => $fieldname
                                    ))) $saves++;
                            }

                            if ($delete) {
                                if ($tbContentUser->delete(
                                    'id_content_user IN (' .
                                        implode(',', array_keys($delete)) .
                                    ')'
                                )) {
                                    $saves++;
                                }
                            }
                            break;

                        case 'contents':
                            $contentRelationships = $content->getRelationships($fieldname);
                            $currContentRelationships = array();
                            foreach($contentRelationships as $contentRelationship) {
                                $currContentRelationships[$contentRelationship->id_content_relationship] =
                                    ($contentRelationship->id_content_a == $content->id
                                        ? $contentRelationship->id_content_b
                                        : $contentRelationship->id_content_a);
                            }

                            $insert = array_diff(
                                    (array) $values['meta'][$fieldname],
                                    $currContentRelationships
                                );

                            $delete = array_diff(
                                    $currContentRelationships,
                                    (array) $values['meta'][$fieldname]
                                );

//                            pr(array(
//                                'current' => $currContentRelationships,
//                                'values'  => (array) $values['meta'][$fieldname],
//                                'insert'  => $insert,
//                                'delete'  => $delete),1);

                            $tbContentRelationship = FrontZend_Container::get('ContentRelationship');

                            if ($insert) foreach($insert as $id_content) {
                                if ($tbContentRelationship->insert(array(
                                        'id_content_a' => $content->id,
                                        'id_content_b' => $id_content,
                                        'rel_type'     => $fieldname
                                    ))) $saves++;
                            }

                            if ($delete) {
                                if ($tbContentRelationship->delete(
                                    'id_content_relationship IN (' .
                                        implode(',', array_keys($delete)) .
                                    ')'
                                )) {
                                    $saves++;
                                }
                            }
                            break;
                        }
                        break;

                    case 'file':
                        if (!isset($values['meta'][$fieldname])) {
                            $values['meta'][$fieldname] = array();
                        }
                        
                        $contentFiles = $content->getFiles($fieldname);
                        $currContentFiles = array();
                        foreach($contentFiles as $contentFile) {
                            $currContentFiles[$contentFile->id_content_file] =
                                $contentFile->id_file;
                        }

                        $insert = array_diff(
                                (array) $values['meta'][$fieldname],
                                $currContentFiles
                            );

                        $update = array_diff_assoc(
                                (array) $values['meta'][$fieldname],
                                array_values($currContentFiles)
                            );

                        $delete = array_diff(
                                $currContentFiles,
                                (array) $values['meta'][$fieldname]
                            );

//                            pr(array(
//                                'current' => $currContentFiles,
//                                'values'  => (array) $values['meta'][$fieldname],
//                                'current_values' => array_values($currContentFiles),
//                                'insert'  => $insert,
//                                'update'  => $update,
//                                'delete'  => $delete),1);


                        $tbContentFile = FrontZend_Container::get('ContentFile');

                        if ($insert) {
                            foreach($insert as $order => $id_file) {
                                if ($tbContentFile->insert(array(
                                        'id_content'   => $content->id,
                                        'id_file'      => $id_file,
                                        'description'  => $fieldname,
                                        'order'        => $order + 1
                                    ))) $saves++;
                            }
                        }

                        if ($update) {
                            foreach($update as $order => $id_file) {
                                if ($tbContentFile->update(
                                    array('order' => $order + 1),
                                    array(
                                        'id_content = ?'   => $content->id,
                                        'id_file = ?'      => $id_file,
                                        'description = ?'  => $fieldname
                                    ))) $saves++;
                            }
                        }

                        if ($delete) {
                            if ($tbContentFile->delete(
                                'id_content_file IN (' .
                                    implode(',', array_keys($delete)) .
                                ')'
                            )) {
                                $saves++;
                            }
                        }
                        break;
                    }
                }
            }

            if (isset($oldSlug) && $oldSlug != $content->slug) {
                $oldPath = 'images/' . $content->id_content_type . '/'
                    . substr($oldSlug, 0, 1) . '/' . $oldSlug;
                $newPath = 'images/' . $content->id_content_type . '/'
                    . substr($content->slug, 0, 1) . '/' . $content->slug;
                FrontZend_Container::get('File')->renameDir($oldPath, $newPath);
            }

            return $saves;
        }
    }

}
