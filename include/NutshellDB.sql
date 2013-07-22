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
  `visible` smallint(6) DEFAULT '1',
  `parametrizable` smallint(6) DEFAULT '0',
  `nota` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `tb_configuracion` */

insert  into `tb_configuracion`(`clave`,`valor`,`mascara`,`seccion`,`orden`,`desc`,`visible`,`parametrizable`,`nota`) values ('LOGO_MINI','img/LogoTIADM.MIN.png','$_SESSION[\'_KEY_\'] = \'_VAL_\';','SESSION',300,'Path Logo (Min)',1,0,NULL),('LOGO_MEDIANO','img/LogoTIADM.MED.png','$_SESSION[\'_KEY_\'] = \'_VAL_\';','SESSION',301,'Path Logo (Med)',1,0,NULL),('LOGO_GRANDE','img/LogoTIADM.BIG.png','$_SESSION[\'_KEY_\'] = \'_VAL_\';','SESSION',302,'Path Logo (Big)',1,0,NULL),('LDAP_ACCOUNT_SUFFIX','','','LDAP',100,'Sufijo de la cuenta',0,1,'Account Suffix'),('LDAP_BASE_DN','','','LDAP',101,'Base DN',0,1,NULL),('LDAP_DC1','','','LDAP',102,'Domain component 1',0,1,'DC1'),('LDAP_DC2','','','LDAP',103,'Domain component 2',0,1,'DC2'),('LDAP_USER','','','LDAP',104,'Username',0,1,NULL),('LDAP_PASS','','','LDAP',105,'Password',0,1,NULL),('LDAP_ON','OFF','','LDAP',106,'LDAP Activo',0,1,'ON/OFF'),('LOGIN_DISABLED','OFF','','SISTEMA',21,'Login ON/OFF',0,0,'ON/OFF'),('MAINTENANCEPAGE','OFF','','SISTEMA',7,'En Mantenimiento',0,1,'ON/OFF'),('COPYRIGHT','Mariano J. Obarrio Miles','$_SESSION[\'_KEY_\'] = \'_VAL_\';','APARIENCIA',5,'Pie de pagina',1,1,NULL),('TRIGGERS','ON','','MYSQL',22,'Habilita / DesHabilita los TRIGGERS',0,0,NULL),('DOMACCESS','\'localhost\',\'192.168.1.4\'','$_SESSION[\'_KEY_\'] = array(_VAL_);','SISTEMA',400,'Dominios Permitidos',1,0,NULL),('SESSION','ini_set(\'memory_limit\', \'-1\');','_VAL_','PHP',0,'Permite utilizar toda la memoria disponible',1,0,NULL),('PATH_URL','http://localhost/Nutshell','$_SESSION[\'_KEY_\'] = \'_VAL_\';','SESSION',3,'HomeDir',1,0,NULL),('TIADM_REV','v1.0-P','$_SESSION[\'_KEY_\'] = \'_VAL_\';','SESSION',401,'Revision del TIADM',1,0,NULL),('LOGOUTPAGE','/logout.php','$_SESSION[\'_KEY_\'] = \'_VAL_\';','SESSION',402,'Pagina de Logout',1,0,NULL),('APP_DOCUMENT_ROOT','/Nutshell','$_SESSION[\'_KEY_\'] = \'_VAL_\';','SESSION',4,'DocumentRoot',1,0,NULL),('APPNAME','Nutshell','$_SESSION[\'_KEY_\'] = \'_VAL_\';','APARIENCIA',1,'Nombre de la aplicacion',1,1,NULL),('APPSESTIMEOUT','600','$_SESSION[\"_KEY_\"] = _VAL_;','SISTEMA',6,'Timeout de sesion',1,1,'Definido en segundos 600 = 10min'),('SESSIONACTIVE','1','$_SESSION[\"_KEY_\"] = _VAL_;','SESSION',404,'Sesion activa no vuelve a recargar los datos',1,0,NULL),('CONFIG_WEBSITE_DOCUMENT_ROOT','/usr/home/exitweb/.ftp-users','$_SESSION[\'_KEY_\'] = \'_VAL_\';','SESSION',405,'Path a eliminar durante un Upload de TinyMCE',1,0,NULL),('APPWELCOMEMSG','Bienvenido al Sistema','$_SESSION[\'_KEY_\'] = \'_VAL_\';','APARIENCIA',2,'Mensaje de bienvenida',1,1,NULL);

/*Table structure for table `tb_usuarios` */

DROP TABLE IF EXISTS `tb_usuarios`;

CREATE TABLE `tb_usuarios` (
  `idUsuario` int(8) NOT NULL AUTO_INCREMENT,
  `Logname` varchar(100) NOT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;

/*Data for the table `tb_usuarios` */

insert  into `tb_usuarios`(`idUsuario`,`Logname`,`Passwd`,`Descripcion`,`Mail`,`Movil`,`active`,`activationDate`,`activationHash`,`InfoLastLoggin`,`LastAccess`,`AccessLevel`) values (1,'nutshell','625cf4d492fe38e88f33c7ee0b4938b0','Usuario de instalacion','',NULL,1,NULL,'','Acceso OK via Local Login','2013-07-22 17:26:31',0),(2,'mobarrio','ddb7eb0685010e592b7598b427ea4d2b','Mariano J. Obarrio Miles','mariano.obarrio@gmail.com','',1,NULL,'','Acceso OK via Local Login','2013-07-22 11:45:21',0);

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
