<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Form
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Content_Form_Content extends Twitter_Bootstrap_Form_Horizontal
{
    protected $_contentType = null;
    protected $_metafields = array();

    public function __construct($options = null)
    {
        $this->setAttrib('id', strtolower(__CLASS__));

        $this->addPrefixPath('FrontZend_Form', 'FrontZend/Form');
        $this->addPrefixPath('Twitter_Bootstrap_Form', 'Twitter/Bootstrap/Form');

        $this->initElements();
        if (isset($options['contentType']) && $options['contentType']) {
            $this->_contentType = $options['contentType'];
            unset($options['contentType']);
            $this->initMetafieldElements();
        }
        $this->initInfo();

        parent::__construct($options);

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
            'class'        => 'input-block-level',
            'multiOptions' => $contentTypes,
        ));
        $groupElements[] = 'id_content_type';

        $this->addElement('text', 'title', array(
            'label' => 'Título',
            'class' => 'input-block-level',
        ));
        $groupElements[] = 'title';

        $this->addElement('text', 'slug', array(
            'label' => 'Slug',
            'class' => 'input-block-level',
            'prepend' => SITE_URL . '/'
        ));
        $groupElements[] = 'slug';

        $this->addElement('datetime', 'dt_published', array(
            'label'  => 'Data de publicação',
            'class'  => 'input-block-level',
            'format' => 'yyyy-MM-dd HH:mm:ss',
            'value'  => Zend_Date::now(),
        ));
        $groupElements[] = 'dt_published';

        $this->addElement('textarea', 'text', array(
            'label' => 'Texto',
            'class' => 'input-block-level',
            'rows'  => 15
        ));
        $groupElements[] = 'text';

        $this->addElement('textarea', 'excerpt', array(
            'label' => 'Resumo',
            'class' => 'input-block-level',
            'rows'  => 4
        ));
        $groupElements[] = 'excerpt';

        $this->addElement('hidden', 'id_content');
        $groupElements[] = 'id_content';

        $this->addElement('text', 'keywords', array(
            'label'   => 'Palavras-chave',
            'class'   => 'input-block-level',
            'prepend' => '<i class="icon-tags"></i>'
        ));
        $groupElements[] = 'keywords';

        $this->addElement('radio', 'status', array(
            'label'        => 'Status',
            'label_class'  => 'inline',
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
                            'class' => 'tab-pane active form-horizontal',
                            'id'    => 'main_elements'
                        )
                    )
                )
            )
        );
    }

    public function initMetafieldElements()
    {
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

            $groupElements = array();
            if($metafields) {
                foreach($metafields as $metafield) {
                    $metaOptions = $metafield->getOptions();
                    $metaOptions['belongsTo'] = 'meta';
                    $metaElement = 'Content_Form_Meta_' . ucfirst($datatype)
                        . '_' . ucfirst($metaOptions[$options['field']]);
                    $this->addElement(new $metaElement($metafield->fieldname,
                        $metaOptions));
                    $groupElements[] = $metafield->fieldname;
                    $this->_metafields[$metafield->fieldname] = $metafield;
                }
            }

            if ($groupElements) {
                $this->addDisplayGroup(
                    $groupElements, $options['displayId'], array(
                        'legend'     => $options['legend'],
                        'decorators' => array(
                            'FormElements',
                            array('HtmlTag', array(
                                    'tag'   => 'div',
                                    'class' => 'tab-pane form-horizontal',
                                    'id'    => $options['displayId']
                                ))
                        )
                    )
                );
            }
        }
    }

    public function initInfo()
    {
        $this->addElement('UneditableTextfield', 'creator_name', array(
            'label'  => 'Cadastrado por',
            'class'  => 'input-block-level',
            'ignore' => true
        ));
        $groupElements[] = 'creator_name';

        $this->addElement('UneditableTextfield', 'date_created', array(
            'label'  => 'Data da criação',
            'class'  => 'input-block-level',
            'ignore' => true
        ));
        $groupElements[] = 'date_created';

        $this->addElement('UneditableTextfield', 'date_updated', array(
            'label'  => 'Última atualização',
            'class'  => 'input-block-level',
            'ignore' => true
        ));
        $groupElements[] = 'date_updated';

        $this->addElement('UneditableTextfield', 'count_comments', array(
            'label'  => 'Comentários',
            'class'  => 'input-block-level',
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
            'label'       => 'Salvar',
            'class'       => 'btn-large',
            'ignore'      => true,
            'buttonType'  => Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY
        ));

        $this->addElement('submit', 'apply', array(
            'label'      => 'Aplicar',
            'class'      => 'btn-large',
            'ignore'     => true,
            'buttonType' => Twitter_Bootstrap_Form_Element_Submit::BUTTON_SUCCESS
        ));

        $this->addElement('submit', 'cancel', array(
            'label'  => 'Cancelar',
            'class'  => 'btn-large',
            'ignore' => true
        ));

        $this->addFormActions(array('save', 'apply', 'cancel'));
    }

    public function init()
    {
        $this->setAttrib('class', 'tab-content');

        $if = Content_Model_Content::getInputFilter();
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
                    if (isset($values['meta'][$fieldname])) {
                        switch($metafield->datatype) {
                        case 'field':
                            $contentMetafield = $content->getMetafield($fieldname);
                            $tbContentMetafield = FrontZend_Container::get('ContentMetafield');
                            if (!$contentMetafield) {
                                $contentMetafield = $tbContentMetafield->createRow();
                                $contentMetafield->id_metafield = $metafield->id;
                                $contentMetafield->id_content = $content->id;
                            }
                            $contentMetafield->value = $values['meta'][$fieldname];
                            if ($tbContentMetafield->save($contentMetafield)) {
                                $saves++;
                            }
                            break;

                        case 'relationship':

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

//                                pr(array(
//                                    'current' => $currContentRelationships,
//                                    'values'  => (array) $values['meta'][$fieldname],
//                                    'insert'  => $insert,
//                                    'delete'  => $delete),1);

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
