-- phpMyAdmin SQL Dump
-- version 3.3.9
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tempo de Geração: Mai 31, 2013 as 12:21 PM
-- Versão do Servidor: 5.5.8
-- Versão do PHP: 5.3.5

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

--
-- Banco de Dados: `frontzend`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `acl_permission`
--

CREATE TABLE IF NOT EXISTS `acl_permission` (
  `id_permission` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_role` varchar(15) NOT NULL,
  `resource` varchar(100) NOT NULL,
  PRIMARY KEY (`id_permission`),
  UNIQUE KEY `id_role` (`id_role`,`resource`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Extraindo dados da tabela `acl_permission`
--


-- --------------------------------------------------------

--
-- Estrutura da tabela `acl_role`
--

CREATE TABLE IF NOT EXISTS `acl_role` (
  `id_role` varchar(15) NOT NULL,
  `role` varchar(30) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `id_parent` varchar(15) DEFAULT NULL,
  `order` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id_role`),
  KEY `id_parent` (`id_parent`),
  KEY `order` (`order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `acl_role`
--

INSERT INTO `acl_role` (`id_role`, `role`, `description`, `id_parent`, `order`) VALUES
('admin', 'Administrador', NULL, 'user', 3),
('guest', 'Visitante', NULL, NULL, 1),
('master', 'Master', 'Usuário com acesso total', NULL, 5),
('super', 'Super administrador', NULL, 'admin', 4),
('user', 'Usuário registrado', NULL, 'guest', 2);

-- --------------------------------------------------------

--
-- Estrutura da tabela `acl_user`
--

CREATE TABLE IF NOT EXISTS `acl_user` (
  `id_user` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_role` varchar(15) NOT NULL,
  `username` varchar(30) NOT NULL,
  `password` char(32) NOT NULL,
  `email` varchar(100) NOT NULL,
  `name` varchar(60) NOT NULL,
  `display_name` varchar(20) NOT NULL,
  `gender` enum('M','F') DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `dt_registered` datetime NOT NULL,
  `dt_updated` datetime NOT NULL,
  `dt_lastaccess` datetime DEFAULT NULL,
  `dt_activated` datetime DEFAULT NULL,
  `activation_key` char(13) NOT NULL,
  `status` enum('A','I','B') NOT NULL DEFAULT 'I',
  PRIMARY KEY (`id_user`),
  UNIQUE KEY `un_username` (`username`),
  UNIQUE KEY `un_email` (`email`),
  KEY `ix_acl_role` (`id_role`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

--
-- Extraindo dados da tabela `acl_user`
--

INSERT INTO `acl_user` (`id_user`, `id_role`, `username`, `password`, `email`, `name`, `display_name`, `gender`, `birthdate`, `avatar`, `dt_registered`, `dt_updated`, `dt_lastaccess`, `dt_activated`, `activation_key`, `status`) VALUES
(1, 'master', 'jaime', '3e173a92a4fd8c2a91b52cfc10c19792', 'animebook@jaimeneto.com', 'Jaime Neto', 'Jaime Neto', NULL, NULL, '', '0000-00-00 00:00:00', '2013-04-30 11:56:30', NULL, NULL, '', 'A');

-- --------------------------------------------------------

--
-- Estrutura da tabela `comment`
--

CREATE TABLE IF NOT EXISTS `comment` (
  `id_comment` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_user` bigint(20) unsigned DEFAULT NULL,
  `id_content` bigint(20) unsigned NOT NULL,
  `answer_to` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(30) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `site` varchar(255) DEFAULT NULL,
  `info` varchar(255) DEFAULT NULL,
  `comments` text NOT NULL,
  `spoiler` tinyint(1) NOT NULL DEFAULT '0',
  `dt_created` datetime NOT NULL,
  `dt_updated` datetime NOT NULL,
  `status` enum('A','I','D') NOT NULL DEFAULT 'A' COMMENT 'A=Active, I=Inactive, D=Deleted',
  PRIMARY KEY (`id_comment`),
  KEY `ix_user` (`id_user`),
  KEY `ix_content` (`id_content`),
  KEY `ix_comment` (`answer_to`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Extraindo dados da tabela `comment`
--


-- --------------------------------------------------------

--
-- Estrutura da tabela `content`
--

CREATE TABLE IF NOT EXISTS `content` (
  `id_content` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_content_type` varchar(15) NOT NULL,
  `id_user` bigint(20) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `text` longtext,
  `excerpt` text,
  `keywords` varchar(255) DEFAULT NULL,
  `dt_created` datetime NOT NULL,
  `dt_updated` datetime NOT NULL,
  `dt_published` datetime NOT NULL,
  `status` enum('A','I','L','D') NOT NULL DEFAULT 'I' COMMENT 'A=Active, I=Inactive, L=Locked, D=Deleted',
  `fixed` tinyint(1) NOT NULL DEFAULT '0',
  `id_group` int(10) unsigned DEFAULT NULL COMMENT 'Grupo de usuários que tem permissão de acesso a esse conteúdo (NULL para todos acessarem)',
  PRIMARY KEY (`id_content`),
  UNIQUE KEY `un_slug` (`slug`),
  UNIQUE KEY `un_title` (`id_content_type`,`title`),
  KEY `ix_user` (`id_user`),
  KEY `ix_type` (`id_content_type`),
  KEY `id_group` (`id_group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Extraindo dados da tabela `content`
--


-- --------------------------------------------------------

--
-- Estrutura da tabela `content_file`
--

CREATE TABLE IF NOT EXISTS `content_file` (
  `id_content_file` bigint(20) NOT NULL AUTO_INCREMENT,
  `id_content` bigint(20) unsigned NOT NULL,
  `id_file` bigint(20) unsigned NOT NULL,
  `description` varchar(30) NOT NULL,
  `legend` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id_content_file`),
  UNIQUE KEY `UNIQUE` (`id_file`,`description`),
  UNIQUE KEY `UN_CONTENT` (`id_content`,`id_file`,`description`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Extraindo dados da tabela `content_file`
--


-- --------------------------------------------------------

--
-- Estrutura da tabela `content_metafield`
--

CREATE TABLE IF NOT EXISTS `content_metafield` (
  `id_content_metafield` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_metafield` int(10) unsigned NOT NULL,
  `id_content` bigint(20) unsigned NOT NULL,
  `value` varchar(255) NOT NULL,
  PRIMARY KEY (`id_content_metafield`),
  UNIQUE KEY `UNIQUE` (`id_metafield`,`id_content`),
  KEY `ix_metafield` (`id_metafield`),
  KEY `ix_content` (`id_content`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Extraindo dados da tabela `content_metafield`
--


-- --------------------------------------------------------

--
-- Estrutura da tabela `content_relationship`
--

CREATE TABLE IF NOT EXISTS `content_relationship` (
  `id_content_relationship` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_content_a` bigint(20) unsigned NOT NULL,
  `id_content_b` bigint(20) unsigned NOT NULL,
  `rel_type` varchar(20) NOT NULL,
  PRIMARY KEY (`id_content_relationship`),
  UNIQUE KEY `UNIQUE` (`id_content_a`,`id_content_b`,`rel_type`),
  KEY `ix_content_a` (`id_content_a`),
  KEY `ix_content_b` (`id_content_b`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Extraindo dados da tabela `content_relationship`
--


-- --------------------------------------------------------

--
-- Estrutura da tabela `content_type`
--

CREATE TABLE IF NOT EXISTS `content_type` (
  `id_content_type` varchar(15) NOT NULL,
  `type` varchar(30) NOT NULL,
  `plural` varchar(30) NOT NULL,
  `id_parent` varchar(15) DEFAULT NULL,
  PRIMARY KEY (`id_content_type`),
  UNIQUE KEY `UNIQUE` (`type`),
  KEY `ix_content_type` (`id_parent`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Extraindo dados da tabela `content_type`
--

INSERT INTO `content_type` (`id_content_type`, `type`, `plural`, `id_parent`) VALUES
('category', 'Categoria', 'Categorias', NULL),
('content', 'Conteúdo', 'Conteúdos', NULL),
('section', 'Seção', 'Seções', NULL);

-- --------------------------------------------------------

--
-- Estrutura da tabela `content_user`
--

CREATE TABLE IF NOT EXISTS `content_user` (
  `id_content_user` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_content` bigint(20) unsigned NOT NULL,
  `id_user` bigint(20) unsigned NOT NULL,
  `rel_type` varchar(20) NOT NULL,
  PRIMARY KEY (`id_content_user`),
  UNIQUE KEY `UNIQUE` (`id_content`,`id_user`,`rel_type`),
  KEY `ix_content` (`id_content`),
  KEY `ix_user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Extraindo dados da tabela `content_user`
--


-- --------------------------------------------------------

--
-- Estrutura da tabela `file`
--

CREATE TABLE IF NOT EXISTS `file` (
  `id_file` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `path` varchar(255) NOT NULL,
  `type` char(3) NOT NULL,
  `credits` varchar(255) DEFAULT NULL,
  `info` text,
  `keywords` varchar(255) DEFAULT NULL,
  `original_name` varchar(100) NOT NULL,
  `id_user` bigint(20) unsigned NOT NULL,
  `dt_created` datetime NOT NULL,
  `dt_updated` datetime NOT NULL,
  PRIMARY KEY (`id_file`),
  UNIQUE KEY `UNIQUE` (`path`),
  KEY `ix_user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Extraindo dados da tabela `file`
--

-- --------------------------------------------------------

--
-- Estrutura da tabela `layout_block`
--

CREATE TABLE IF NOT EXISTS `layout_block` (
  `id_layout_block` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `id_layout_page` int(10) unsigned NOT NULL,
  `id_wrapper` bigint(20) unsigned DEFAULT NULL,
  `id_parent` bigint(20) unsigned DEFAULT NULL,
  `module` varchar(30) NOT NULL,
  `block` varchar(50) NOT NULL,
  `order` int(11) NOT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT '0',
  `options` text,
  PRIMARY KEY (`id_layout_block`),
  UNIQUE KEY `UNIQUE` (`id_layout_page`,`id_wrapper`,`order`),
  KEY `id_parent` (`id_parent`),
  KEY `id_layout_page` (`id_layout_page`),
  KEY `id_wrapper` (`id_wrapper`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Extraindo dados da tabela `layout_block`
--


-- --------------------------------------------------------

--
-- Estrutura da tabela `layout_page`
--

CREATE TABLE IF NOT EXISTS `layout_page` (
  `id_layout_page` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `page` varchar(30) NOT NULL,
  `id_content` bigint(20) unsigned DEFAULT NULL,
  `id_content_type` varchar(15) DEFAULT NULL,
  `special` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`id_layout_page`),
  UNIQUE KEY `un_page` (`page`),
  UNIQUE KEY `UNIQUE` (`id_content`,`id_content_type`),
  KEY `ix_content_type` (`id_content_type`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=28 ;

--
-- Extraindo dados da tabela `layout_page`
--

INSERT INTO `layout_page` (`id_layout_page`, `page`, `id_content`, `id_content_type`, `special`) VALUES
(1, 'Principal', NULL, NULL, 'home'),
(2, 'Busca', NULL, NULL, 'search'),
(3, 'Contato', NULL, NULL, 'contact'),
(4, 'Usuário / Cadastro', NULL, NULL, 'user-signin'),
(5, 'Usuário / Perfil', NULL, NULL, 'user-profile'),
(6, 'Usuário / Amigos', NULL, NULL, 'user-friends'),
(7, 'Usuário / Mensagens', NULL, NULL, 'user-messages'),
(8, 'Usuário / Lista', NULL, NULL, 'user-list');

-- --------------------------------------------------------

--
-- Estrutura da tabela `metafield`
--

CREATE TABLE IF NOT EXISTS `metafield` (
  `id_metafield` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `id_content_type` varchar(15) NOT NULL,
  `datatype` varchar(15) NOT NULL,
  `fieldname` varchar(20) NOT NULL,
  `options` text NOT NULL,
  `order` int(11) NOT NULL,
  PRIMARY KEY (`id_metafield`),
  UNIQUE KEY `UNIQUE` (`id_content_type`,`fieldname`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

--
-- Extraindo dados da tabela `metafield`
--


-- --------------------------------------------------------


--
-- Restrições para as tabelas dumpadas
--

--
-- Restrições para a tabela `acl_permission`
--
ALTER TABLE `acl_permission`
  ADD CONSTRAINT `acl_permission_ibfk_1` FOREIGN KEY (`id_role`) REFERENCES `acl_role` (`id_role`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para a tabela `acl_role`
--
ALTER TABLE `acl_role`
  ADD CONSTRAINT `acl_role_ibfk_1` FOREIGN KEY (`id_parent`) REFERENCES `acl_role` (`id_role`) ON UPDATE CASCADE;

--
-- Restrições para a tabela `comment`
--
ALTER TABLE `comment`
  ADD CONSTRAINT `comment_ibfk_2` FOREIGN KEY (`id_content`) REFERENCES `comment` (`id_comment`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `comment_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `acl_user` (`id_user`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_comment_comment` FOREIGN KEY (`answer_to`) REFERENCES `comment` (`id_comment`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Restrições para a tabela `content`
--
ALTER TABLE `content`
  ADD CONSTRAINT `content_ibfk_2` FOREIGN KEY (`id_user`) REFERENCES `acl_user` (`id_user`) ON UPDATE CASCADE,
  ADD CONSTRAINT `content_ibfk_1` FOREIGN KEY (`id_content_type`) REFERENCES `content_type` (`id_content_type`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Restrições para a tabela `layout_block`
--
ALTER TABLE `layout_block`
  ADD CONSTRAINT `layout_block_ibfk_1` FOREIGN KEY (`id_layout_page`) REFERENCES `layout_page` (`id_layout_page`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `layout_block_ibfk_2` FOREIGN KEY (`id_parent`) REFERENCES `layout_block` (`id_layout_block`) ON UPDATE CASCADE,
  ADD CONSTRAINT `layout_block_ibfk_3` FOREIGN KEY (`id_wrapper`) REFERENCES `layout_block` (`id_layout_block`) ON DELETE CASCADE ON UPDATE CASCADE;
