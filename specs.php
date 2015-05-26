/**
 *
 *	Here goes all the application logic
 *	of routing, dispatching
 *	data render and controller/model actions
 *
 *	Finally, we need to render some view 
 *	and send back to the client.
 *	
 *
 * 	The view is a pure html template
 *	with reserved anchors in places, where
 *	we need to inject/replace with live data.
 *
 *	The main point is to stay with pure html content for:
 *  	1. Passing HTML validation 
 *		 2. Browser can show the template to the client (with some 'lorem ipsum'), no server needed.
 *		 3. Front-end designer knows about "reserved markup", but focused anly on html, css and js.
 *			 (without the server, the template just another HTML Template. 
 *			 But when this template running trough the server engine, all placeholder are replaced by live data).
 *		 4. Ability to preview/edit the template in WYSIWYG editor.
 *		 5. IDE/WYSIWYG editor, that can understand specific "reserved markup", can generate customized dynamic template blocks/widgets.
 *
 *	To be discuss:
 *		 1. Find middle between "reserved markup" and "rendering instructions".
 *			"reserved markup" - The template includes all the instructions for rendering.
 *			"instructions for rendering" - How the data needs to be binded. may be included in render controller.
 *   2. Do we need Controller Class(data, instructions) to all templates block?
 *		 3. Performance issue: time/memory(parsing, binding) and functionality(user interface for designer/developers). (Can be not usefull)
 *   4. Maybe it's combination of 
 *     AngularJS (@see https://docs.angularjs.org/guide/templates) and 
 *     TAL (@see http://en.wikipedia.org/wiki/Template_Attribute_Language)
 *
 **/


 /**
  *  Renderind example - basic Template Engine API
  */
  <?php

  $html_template1 = "
  		<ul data-use='items' data-continue='repeat'>
  			<li>
  				<a href='#' data-atribute='href href' data-text='label'>Example Text 1</a>
  			</li>
  			<li data-continue='repeat'>
  				<a href='#' data-atribute='href href' data-text='label'>Example Text 2</a>
  			</li>
  			<li data-continue='repeat'>
  				<a href='#' data-atribute='href href' data-text='label'>Example Text 3</a>
  			</li>
  		</ul>";
  // OR widget block
  $html_template2 = file_get_contents('/path/to/my_widget.html');
  // OR full page
  $html_template3 = file_get_contents('/path/to/my_page1.html');



  // Exaple 1, all rendering logic in the template block
  // 
  $view = new \Insite\Engine\Templater\Templater(); // or $view = new Templater($html_template1); 

  $data = array(
  		"items" => array(
  			array("href" => 'www.google.com', "label" => 'Google'),
  			array("href" => 'www.yahoo.com', "label" => 'Yahoo')
  		)
  );

  $view
  	->load($html_template1)
  	->bind($data)
  	->find('ul')
  	->after('<p>Parsed!</p>');

  $html_live = $view->fetch(); // or maybe $view->send();
  echo $html_live;

  /**
   *  Output: (Do we need to remove all template markup?)
   *
   *  	<ul>
   *		<li>
   *			<a href='www.google.com'>Google</a>
   *		</li>
   *		<li>
   *			<a href='www.yahoo.com'>Yahoo</a>
   *		</li>
   *	</ul> 
   *	<p>Parsed!</p>
   */


  // Example 2, simple logic in template, has own Controller 

  $html_template2 = "
  		<ul data-model='MyWidgets'>
  			<li>
  				<a href='#' data-atribute='href href' data-text='getLabel'>Example Text 1</a>
  			</li>
  			<li data-continue='repeat'>
  				<a href='#' data-atribute='href href' data-text='getLabel'>Example Text 2</a>
  			</li>
  			<li data-continue='repeat'>
  				<a href='#' data-atribute='href href' data-text='getLabel'>Example Text 3</a>
  			</li>
  		</ul>";


  /**
  *  Class MyWidget
  */
  class MyWidget extends \Insite\Engine\BaseViewController {
  	
  	public $href;
  	private $label;

  	public function __construct($href, $label) {
  		$this->href  = $href;
  		$this->label = $label;
  	}

  	public function getLabel() {
  		return $this->label;
  	}

  }

  /**
  *  Class MyWidgets
  */
  class MyWidgets extends \Insite\Engine\ListViewController {
  	
  	public $widgets;

  	public function __construct($href, $label) {
  		$allLinks = $this->getFromDB();
  		foreach ($allLinks as $key => $link) {
  			$this->widgets[] = new MyWidget($link['href'], $link['label']);
  		}
  	}

  	private function getFromDB() {
  		$fromDB_sample = array(
  			array("href" => 'www.google.com', "label" => 'Google'),
  			array("href" => 'www.yahoo.com', "label" => 'Yahoo')
  		);
  		return $fromDB;
  	}

  }

  // Because data-model is instance of ListViewController the template will render from all items (loop)
  // If data-model is instance of BaseViewController the view will rendered as one item
  $view = new \Insite\Engine\Templater\Templater($html_template2);
  $html_live = $view->fetch(); 
  echo $html_live;



  ?>
