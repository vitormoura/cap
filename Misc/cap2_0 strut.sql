# MySQL-Front Dump 2.5
#
# Host: localhost   Database: intranet
# --------------------------------------------------------
# Server version 4.0.14-nt

CREATE DATABASE intranet;
USE intranet;


#
# Table structure for table 'anotacao'
#

CREATE TABLE anotacao (
  codNote int(11) NOT NULL auto_increment,
  codColab int(3) unsigned NOT NULL default '0',
  nota tinytext NOT NULL,
  PRIMARY KEY  (codNote)
) TYPE=MyISAM;



#
# Table structure for table 'banco'
#

CREATE TABLE banco (
  codBanco int(3) unsigned NOT NULL auto_increment,
  codColab int(3) unsigned default NULL,
  numero char(3) NOT NULL default '',
  nome varchar(30) NOT NULL default '',
  PRIMARY KEY  (codBanco)
) TYPE=MyISAM;



#
# Table structure for table 'colaborador'
#

CREATE TABLE colaborador (
  codColab int(3) unsigned NOT NULL auto_increment,
  codGrupo tinyint(3) unsigned NOT NULL default '0',
  pessoa enum('j','f') NOT NULL default 'j',
  nome varchar(60) NOT NULL default '',
  fantasia varchar(20) NOT NULL default '',
  inscFed varchar(20) NOT NULL default '',
  inscEst varchar(20) default NULL,
  website varchar(100) default NULL,
  endereco varchar(60) NOT NULL default '',
  cidade varchar(20) NOT NULL default '',
  estado char(2) NOT NULL default '',
  cep varchar(10) NOT NULL default '',
  PRIMARY KEY  (codColab)
) TYPE=MyISAM;



#
# Table structure for table 'conta'
#

CREATE TABLE conta (
  codConta tinyint(3) unsigned NOT NULL auto_increment,
  codBanco int(3) unsigned NOT NULL default '0',
  codLoja int(3) unsigned NOT NULL default '0',
  agencia varchar(10) NOT NULL default '',
  cc varchar(15) NOT NULL default '',
  permissoes tinyint(1) unsigned NOT NULL default '3',
  PRIMARY KEY  (codConta)
) TYPE=MyISAM;



#
# Table structure for table 'documento'
#

CREATE TABLE documento (
  codDoc bigint(20) unsigned NOT NULL auto_increment,
  codTp tinyint(2) unsigned NOT NULL default '0',
  codPlano int(3) unsigned NOT NULL default '0',
  CodNote int(11) default NULL,
  codUser tinyint(3) unsigned NOT NULL default '0',
  loja int(3) unsigned NOT NULL default '0',
  fornecedor int(3) unsigned NOT NULL default '0',
  numero varchar(12) NOT NULL default '',
  gravacao int(10) unsigned NOT NULL default '0',
  emissao int(10) NOT NULL default '0',
  vencimento int(10) NOT NULL default '0',
  valor float(8,2) NOT NULL default '0.00',
  contabil enum('s','n') NOT NULL default 's',
  historico tinytext,
  PRIMARY KEY  (codDoc)
) TYPE=MyISAM;



#
# Table structure for table 'email'
#

CREATE TABLE email (
  codMail int(3) unsigned NOT NULL auto_increment,
  codColab int(3) unsigned NOT NULL default '0',
  endereco varchar(40) NOT NULL default '',
  comentario varchar(50) default NULL,
  PRIMARY KEY  (codMail)
) TYPE=MyISAM;



#
# Table structure for table 'formapag'
#

CREATE TABLE formapag (
  codForma tinyint(3) unsigned NOT NULL auto_increment,
  formaPag varchar(20) NOT NULL default '',
  permissoes tinyint(1) unsigned NOT NULL default '3',
  PRIMARY KEY  (codForma)
) TYPE=MyISAM;



#
# Table structure for table 'grupo'
#

CREATE TABLE grupo (
  codGrupo tinyint(3) unsigned NOT NULL auto_increment,
  grupo varchar(25) NOT NULL default '',
  classes varchar(12) NOT NULL default '',
  PRIMARY KEY  (codGrupo)
) TYPE=MyISAM;



#
# Table structure for table 'lembrete'
#

CREATE TABLE lembrete (
  codLemb int(10) unsigned NOT NULL auto_increment,
  tipo varchar(20) NOT NULL default '',
  ref varchar(15) default NULL,
  lida enum('s','n') NOT NULL default 'n',
  dtLemb int(10) NOT NULL default '0',
  mensagem tinytext NOT NULL,
  PRIMARY KEY  (codLemb)
) TYPE=MyISAM;



#
# Table structure for table 'loja'
#

CREATE TABLE loja (
  codLoja int(3) unsigned NOT NULL auto_increment,
  codGrupo tinyint(3) unsigned NOT NULL default '0',
  pessoa enum('j','f') NOT NULL default 'j',
  nome varchar(60) NOT NULL default '',
  fantasia varchar(20) NOT NULL default '',
  inscFed varchar(20) NOT NULL default '',
  inscEst varchar(20) default NULL,
  website varchar(100) default NULL,
  endereco varchar(60) NOT NULL default '',
  cidade varchar(20) NOT NULL default '',
  estado char(2) NOT NULL default '',
  cep varchar(10) NOT NULL default '',
  PRIMARY KEY  (codLoja)
) TYPE=MyISAM;



#
# Table structure for table 'movimentacao'
#

CREATE TABLE movimentacao (
  codMov bigint(20) unsigned NOT NULL auto_increment,
  codForma tinyint(3) unsigned NOT NULL default '0',
  codConta tinyint(3) unsigned NOT NULL default '0',
  dtMov int(10) unsigned NOT NULL default '0',
  numero varchar(8) default NULL,
  nPagamentos tinyint(3) unsigned NOT NULL default '0',
  valor float(8,2) NOT NULL default '0.00',
  tipo enum('d','c') NOT NULL default 'd',
  PRIMARY KEY  (codMov)
) TYPE=MyISAM;



#
# Table structure for table 'pagamento'
#

CREATE TABLE pagamento (
  codPag bigint(20) unsigned NOT NULL auto_increment,
  codDoc bigint(20) unsigned NOT NULL default '0',
  codMov bigint(20) unsigned NOT NULL default '0',
  vencimento int(10) NOT NULL default '0',
  aPagar float(8,2) NOT NULL default '0.00',
  descontos float(8,2) NOT NULL default '0.00',
  acrescimos float(8,2) NOT NULL default '0.00',
  abatimentos float(8,2) NOT NULL default '0.00',
  comentario varchar(50) default NULL,
  situacao char(1) NOT NULL default 'n',
  PRIMARY KEY  (codPag)
) TYPE=MyISAM;



#
# Table structure for table 'telefone'
#

CREATE TABLE telefone (
  codTel int(4) unsigned NOT NULL auto_increment,
  codColab int(3) unsigned default NULL,
  contato varchar(40) default NULL,
  numero varchar(15) default NULL,
  PRIMARY KEY  (codTel)
) TYPE=MyISAM;



#
# Table structure for table 'tpdoc'
#

CREATE TABLE tpdoc (
  codTp tinyint(2) unsigned NOT NULL auto_increment,
  tipo varchar(30) default NULL,
  PRIMARY KEY  (codTp)
) TYPE=MyISAM;



#
# Table structure for table 'usuario'
#

CREATE TABLE usuario (
  codUser tinyint(3) unsigned NOT NULL auto_increment,
  codFunc int(3) NOT NULL default '0',
  login varchar(10) NOT NULL default '',
  senha varchar(40) NOT NULL default '',
  nivel tinyint(1) unsigned NOT NULL default '0',
  pref varchar(20) default 'default.css',
  PRIMARY KEY  (codUser)
) TYPE=MyISAM;

