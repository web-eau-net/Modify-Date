<?php
/**
 * @package     Joomla.Plugin
 * @subpackage  Content.modifydate
 *
 * @copyright   (C) 2007 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\Factory;
/**
 * Joomla! SEF Plugin.
 *
 * @since  1.5
 */
class PlgSystemModifyDate extends CMSPlugin
{
	/**
	 * Application object.
	 *
	 * @var    JApplicationCms
	 * @since  3.5
	 */
	protected $app;

	/**
	 * Add the canonical uri to the head.
	 *
	 * @return  void
	 *
	 * @since   3.5
	 */
	public function onAfterDispatch()
	{
	$doc = $this->app->getDocument();
  // equivalent of $app = JFactory::getApplication();
       $ismodified = (int) $this->params->get('modify_date', 0);
      $dateformate = JText::_( 'DATE_FORMAT_CALENDAR_DATETIME');
       if( $ismodified === 1){       
		 $calendar = JHTML::calendar('','jform[modified_new]', 'jform_modified', $dateformate,array('size'=>'22','class'=>' required','showTime'=>true));
		 $calendar = preg_replace("/\s+|\n+|\r/", ' ', $calendar);
        $input = $this->app->input;
        $option = $input->get('option');
        $view = $input->get('view');
        $layout = $input->get('layout');
          $id = $input->get('id');
	if($option=='com_content' && $view=='article' && $layout=='edit' && isset($id)){
	    
	    $db = Factory::getDBO();
$query = $db->getQuery(true);
$query->select('*');
$query->from('#__content'); 
$query->where('id = '.    $id );   //put your condition here    
$db->setQuery($query);
$result = $db->loadObjectList();
$resultmodified='';
if(!empty($result)){
    $result = $result[0];
    $resultmodified =  $result->modified;
}

if($resultmodified){ 		
    $document = JFactory::getDocument();
	$doc->addScriptDeclaration("
	window.onload = (event) => {
		 document.getElementById('jform_modified').removeAttribute('readonly');
		 var valued = '".$resultmodified."';
		 console.log(valued);
		  document.getElementById('jform_modified').parentNode.setAttribute('id','modifydated');

		 document.getElementById('modifydated').parentNode.setAttribute('id','modifydatedid');
		  var elem = document.getElementById('jform_modified_btn');
    	elem.parentNode.removeChild(elem);
      	var elem = document.getElementById('jform_modified');
    	elem.parentNode.removeChild(elem);
		document.getElementById('modifydatedid').innerHTML ='".$calendar."';
		var elements = document.querySelectorAll('#modifydatedid .field-calendar'); 
    	for (i = 0; i < elements.length; i++) {            
        JoomlaCalendar.init(elements[i]); 
 		} 
 		document.getElementById('jform_modified').setAttribute('value',valued);
 		document.getElementById('jform_modified').setAttribute('data-alt-value',valued);
 		
		};
			");
    
}else{

		$document = JFactory::getDocument();
	$doc->addScriptDeclaration("
	window.onload = (event) => {
		 document.getElementById('jform_modified').removeAttribute('readonly');
		 var valued = document.getElementById('jform_modified').value;
		 console.log(valued);
		  document.getElementById('jform_modified').parentNode.setAttribute('id','modifydated');

		 document.getElementById('modifydated').parentNode.setAttribute('id','modifydatedid');
		  var elem = document.getElementById('jform_modified_btn');
    	elem.parentNode.removeChild(elem);
      	var elem = document.getElementById('jform_modified');
    	elem.parentNode.removeChild(elem);
		document.getElementById('modifydatedid').innerHTML ='".$calendar."';
		var elements = document.querySelectorAll('#modifydatedid .field-calendar'); 
    	for (i = 0; i < elements.length; i++) {            
        JoomlaCalendar.init(elements[i]); 
 		} 
 		document.getElementById('jform_modified').setAttribute('value',valued);
 		document.getElementById('jform_modified').setAttribute('data-alt-value',valued);
 		
		};
			");
			
}

			}
		}else{
			return;
		}

		//die('here');
	}

	/**
	 * Convert the site URL to fit to the HTTP request.
	 *
	 * @return  void
	 */
	public function onAfterRender()
	{

	}

	/**
	 * Convert the updated the modified the date of article.
	 *
	 * @return  void
	 */
	 function onContentAfterSave($context, $article, $isNew) {
        $ismodified = (int) $this->params->get('modify_date', 0);
         $input = $this->app->input;
        $option = $input->get('option');
        $view = $input->get('view');
        $layout = $input->get('layout');
        $id = $input->get('id');

       if( $ismodified === 1 &&  isset($id)){
    
       	if(isset($_POST['jform']['modified_new'])){
       	$date_forchange= date('Y-m-d H:i:s',strtotime($_POST['jform']['modified_new']));
       	 //$article->modified =$_POST['jform']['modified_new'];
        $db = JFactory::getDbo();
	    $query = $db->getQuery(true);
	    $fields = array('modified');
	    $fields = array(
        $db->quoteName('modified') . ' = ' . $db->quote($date_forchange));
	    $conditions = array($db->quoteName('id') . ' = '.$id );

		$query->update($db->quoteName('#__content'))->set($fields)->where($conditions);
		echo $query; 
		$db->setQuery($query);
		$result = $db->execute();


		$query = $db->getQuery(true);

// Select all records from the user profile table where key begins with "custom.".
// Order it by the ordering field.
/*$query->select($db->quoteName(array('modified', 'id')));
$query->from($db->quoteName('#__content'));
$query->where($db->quoteName('id') . ' = ' . $db->quote($id));
$query->order('ordering ASC');

// Reset the query using our newly populated query object.
$db->setQuery($query);

// Load the results as a list of stdClass objects (see later for more options on retrieving data).
$results = $db->loadObjectList();

print_r($results);

die();*/
		}
      }
		return true;
	}


	/**
	 * Check the buffer.
	 *
	 * @param   string  $buffer  Buffer to be checked.
	 *
	 * @return  void
	 */
	private function checkBuffer($buffer)
	{

	}
}
