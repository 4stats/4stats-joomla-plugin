<?php

/**
 * 4stats Web Analytics Plugin for Joomla
 *
 * @version     1.0
 * @copyright   Copyright (C) 2014 4stats. All rights reserved.
 * @license     http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * @category    Joomla Plugin
 * @author      Nico Puhlmann <nico@4stats.de>
 * 
 */

defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin');
jimport( 'joomla.application.application' );
jimport( 'joomla.client.helper' );
jimport( 'joomla.html.html' );

class plgSystem4stats extends JPlugin
{
	
	function onBeforeRender()
	{
		// get and integrate tracking code.
		$trackingCode = $this->trackingCode();
		$doc = &JFactory::getDocument();
		$doc->addCustomTag($trackingCode);
	}

	/**
	 * trackingCode - generate the 4stats tracking code.
	 * @param null
	 * @return string
	 */
	function trackingCode(){
		
		
		$application = &JFactory::getApplication();
		
		// params
		$enabled = $this->params->get('enabled', 0);
		$project_id = $this->params->get('project_id', '');
		$ignoreadmins = $this->params->get('ignoreadmins', 0);
		$trackuser = $this->params->get('trackuser', 0);

        
        if($enabled != 1 || !$project_id || ($ignoreadmins == 1 && $application->isAdmin()) || JRequest::getVar('format','html') != 'html')
		{
			return '';
		}
		
		// get application information
		$application =& JFactory::getApplication('site');
		$application->initialise();
		$user = &JFactory::getUser();
		
		error_log("hier: " . $ignoreadmins . " - " . $enabled);
		error_log(print_r($user, true));
		$trackingCode = "<script>\r\n";
		$trackingCode .= "var _fss=_fss||{}; _fss.siteId = {$project_id};\r\n";
		if($trackuser == 1 && $user->guest == 0)
		{
			$trackingCode .= "_fss.identify = {username:'" . $user->username . "', name:'" . $user->name . "', email:'" . $user->email . "', registerdate:'" . $user->registerDate . "'};\r\n";
		}
		$trackingCode .= '(function(){var e="fourstats",a=window,c=["track","identify","config","register"],b=function(){var d=0,f=this;for(f._fs=[],d=0;c.length>d;d++){(function(j){f[j]=function(){return f._fs.push([j].concat(Array.prototype.slice.call(arguments,0))),f}})(c[d])}};a[e]=a[e]||new b;var i=document;var h=i.createElement("script");h.type="text/javascript";h.async=true;h.src=document.location.protocol+"//4stats.de/track.js";var g=i.getElementsByTagName("script")[0];g.parentNode.insertBefore(h,g)})();' . "\r\n";
		$trackingCode .= "</script>\r\n";
		

		return $trackingCode;
	}
}
