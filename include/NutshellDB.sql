/*
SQLyog Ultimate v11.11 (64 bit)
MySQL - 5.5.27 : Database - nutshell
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`nutshell` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `nutshell`;

/*Table structure for table `tb_configuracion` */

DROP TABLE IF EXISTS `tb_configuracion`;

CREATE TABLE `tb_configuracion` (
  `clave` varchar(50) DEFAULT NULL,
  `valor` varchar(120) DEFAULT NULL,
  `mascara` varchar(100) DEFAULT NULL,
  `seccion` varchar(20) DEFAULT NULL,
  `orden` smallint(4) DEFAULT NULL,
  `desc` varchar(100) DEFAULT NULL,
  `visible` smallint(6) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `tb_configuracion` */

insert  into `tb_configuracion`(`clave`,`valor`,`mascara`,`seccion`,`orden`,`desc`,`visible`) values ('LOGO_MINI','img/LogoTIADM.MIN.png','$_SESSION[\'_KEY_\'] = \'_VAL_\';','SESSION',300,'Path Logo (Min)',1),('LOGO_MEDIANO','img/LogoTIADM.MED.png','$_SESSION[\'_KEY_\'] = \'_VAL_\';','SESSION',301,'Path Logo (Med)',1),('LOGO_GRANDE','img/LogoTIADM.BIG.png','$_SESSION[\'_KEY_\'] = \'_VAL_\';','SESSION',302,'Path Logo (Big)',1),('LDAP_ACCOUNT_SUFFIX','','','LDAP',100,'ACCOUNT SUFFIX',0),('LDAP_BASE_DN','','','LDAP',101,'BASE DN',0),('LDAP_DC1','','','LDAP',102,'DC1',0),('LDAP_DC2','','','LDAP',103,'DC2',0),('LDAP_USER','','','LDAP',104,'Username',0),('LDAP_PASS','','','LDAP',105,'Password',0),('LDAP_ON','OFF','','LDAP',106,'LDAP ON/OFF',0),('LOGIN_DISABLED','OFF','','TIADM',400,'Login ON/OFF',0),('MAINTENANCEPAGE','OFF','','TIADM',401,'Maintenance ON/OFF',0),('COPYRIGHT','Mariano J. Obarrio Miles','$_SESSION[\'_KEY_\'] = \'_VAL_\';','SESSION',304,'Pie de pagina',1),('TRIGGERS','ON','','MYSQL',200,'Habilita / DesHabilita los TRIGGERS',0),('DOMACCESS','\'localhost\'','$_SESSION[\'_KEY_\'] = array(_VAL_);','TIADM',402,'Dominios Permitidos',1),('SESSION','ini_set(\'memory_limit\', \'-1\');','_VAL_','PHP',0,'Permite utilizar toda la memoria disponible',1),('PATH_URL','http://localhost/Nutshell','$_SESSION[\'_KEY_\'] = \'_VAL_\';','SESSION',305,'HomeDir',1),('TIADM_REV','v1.0-P','$_SESSION[\'_KEY_\'] = \'_VAL_\';','SESSION',306,'Revision del TIADM',1),('LOGOUTPAGE','/logout.php','$_SESSION[\'_KEY_\'] = \'_VAL_\';','SESSION',306,'Pagina de Logout',1),('APP_DOCUMENT_ROOT','/Nutshell','$_SESSION[\'_KEY_\'] = \'_VAL_\';','SESSION',308,'DocumentRoot',1),('APPNAME','Nutshell','$_SESSION[\'_KEY_\'] = \'_VAL_\';','SESSION',307,'Nombre de la aplicacion',1),('APPSESTIMEOUT','600','$_SESSION[\"_KEY_\"] = _VAL_;','SESSION',309,'Tiempo de expliracion en segundos 600 = 10min',1),('SESSIONACTIVE','1','$_SESSION[\"_KEY_\"] = _VAL_;','SESSION',310,'Sesion activa no vuelve a recargar los datos',1),('CONFIG_WEBSITE_DOCUMENT_ROOT','/usr/home/exitweb/.ftp-users','$_SESSION[\'_KEY_\'] = \'_VAL_\';','SESSION',311,'Path a eliminar durante un Upload de TinyMCE',1);

/*Table structure for table `tb_usuarios` */

DROP TABLE IF EXISTS `tb_usuarios`;

CREATE TABLE `tb_usuarios` (
  `idUsuario` varchar(100) NOT NULL,
  `Passwd` varchar(55) DEFAULT '',
  `Descripcion` varchar(50) DEFAULT '',
  `Mail` varchar(40) DEFAULT '',
  `Movil` varchar(13) DEFAULT NULL,
  `active` tinyint(4) DEFAULT '0',
  `activationDate` datetime DEFAULT NULL,
  `activationHash` varchar(150) DEFAULT '',
  `InfoLastLoggin` varchar(60) DEFAULT NULL,
  `LastAccess` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `AccessLevel` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`idUsuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `tb_usuarios` */

insert  into `tb_usuarios`(`idUsuario`,`Passwd`,`Descripcion`,`Mail`,`Movil`,`active`,`activationDate`,`activationHash`,`InfoLastLoggin`,`LastAccess`,`AccessLevel`) values ('nutshell','625cf4d492fe38e88f33c7ee0b4938b0','Usuario de instalacion','',NULL,1,NULL,'','Acceso OK via Local Login','2013-07-20 00:06:32',0);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
