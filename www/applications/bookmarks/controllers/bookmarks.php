<?php
/**
 * Access from index.php:
 */
if(!defined("_access")) {
	die("Error: You don't have permission to access here...");
}

class Bookmarks_Controller extends ZP_Controller {
	
	public function __construct() {		
		$this->Templates = $this->core("Templates");
		$this->Cache     = $this->core("Cache");
		
		$this->application = $this->app("bookmarks");
		
		$this->Templates->theme();

		$this->config("bookmarks");
		
		$this->Bookmarks_Model = $this->model("Bookmarks_Model");

		$this->helper("pagination");
	}
	
	public function index($bookmarkID = 0) {
		$this->meta("language", whichLanguage(FALSE));
                
		if($bookmarkID !== "add") {
			if($bookmarkID > 0) {
				$this->go($bookmarkID);
			} else {
				$this->getBookmarks();
			}
		}
	}

	public function rss() {
		$this->helper("time");

		$data = $this->Bookmarks_Model->getRSS();
		
		if($data) {
			$vars["bookmarks"]= $data;	

			$this->view("rss", $vars, $this->application);
		} else {
			redirect();
		}

	}

	public function add() {
		isConnected();
		
		if(POST("save")) {
			$vars["alert"] = $this->Bookmarks_Model->add();
		} 

		if(POST("preview")) {
			$this->helper("time");

			$this->title(__("Bookmarks") ." - ". htmlentities(encode(POST("title", "decode", NULL)), ENT_QUOTES, "UTF-8"));

			$data = $this->Bookmarks_Model->preview();

			if($data) {
				$this->CSS("bookmarks", $this->application);
				$this->js("preview", $this->application);
				
				$this->config("user", "bookmarks");

				$vars["bookmark"] = $data;
				$vars["view"] 	  = $this->view("preview", TRUE);
			
				$this->render("content", $vars);
			} else {
				redirect();
			}
		} else {
			$this->CSS("forms", "cpanel");

			$this->helper(array("html", "forms", "tags"));

			$this->config("user", "bookmarks");

			$vars["view"] = $this->view("new", TRUE);

			$this->render("content", $vars);
		}

	}

	public function admin() {
		isConnected();

		$this->config("user", "bookmarks");

		$data = $this->Bookmarks_Model->getAllByUser();

		$this->CSS("results", "cpanel");
		$this->CSS("admin", "bookmarks");

		if($data) {
			$vars["tFoot"] = $data;
			$total = count($data);
		} else {
			$vars["tFoot"] = array();
			$total = 0;
		}

		$label = ($total === 1 ? __("record") : __("records"));

		$vars["total"] = (int)$total . " $label";
		
		$vars["view"] = $this->view("admin", TRUE);
		$this->render("content", $vars);
	}

	public function author($user = NULL, $bookmarkID = NULL, $slug = NULL) {
		if($user === NULL) {
			redirect();
		} elseif($bookmarkID === NULL and $slug === NULL) {
			$this->getBookmarksByAuthor($user);
		} elseif($bookmarkID !== "tag") {
			$this->index($bookmarkID, $slug);
		}
	}

	public function like($ID) {
		$this->Users_Model = $this->model("Users_Model");

		$this->Users_Model->setLike($ID, "bookmarks", 10);
	}

	public function dislike($ID) {
		$this->Users_Model = $this->model("Users_Model");

		$this->Users_Model->setDislike($ID, "bookmarks", 10);
	}

	public function report($ID) {
		$this->Bookmarks_Model->setReport($ID, "bookmarks", 10);
	}	

	public function tag($tag) {
		$this->title(__("Bookmarks"));
		$this->CSS("bookmarks", $this->application);
		$this->CSS("pagination");
		
		$limit = $this->limit($tag);

		$data = $this->Cache->data("tag-$tag-$limit", "bookmarks", $this->Bookmarks_Model, "getByTag", array($tag, $limit));

		if($data) {
			$this->meta("keywords", $data[0]["Tags"]);
			$this->meta("description", $data[0]["Description"]);
			$this->helper("time");

			$vars["bookmarks"]  = $data;
			$vars["pagination"] = $this->pagination;
			$vars["view"]       = $this->view("bookmarks", TRUE);
			
			$this->render("content", $vars);
		} else {
			redirect();
		}
	}

	public function go($bookmarkID = 0) {
		$this->CSS("bookmarks", $this->application);
		$this->CSS("pagination");

		$data = $this->Cache->data("bookmark-$bookmarkID", "bookmarks", $this->Bookmarks_Model, "getByID", array($bookmarkID));

		if($data) {
			$this->helper("time");

			$this->title(__("Bookmarks") ." - ". decode($data[0]["Title"]), FALSE);
			$this->meta("keywords", $data[0]["Tags"]);
			$this->meta("description", $data[0]["Description"]);
                        
			$this->Bookmarks_Model->updateViews($bookmarkID);

			$vars["bookmark"] = $data[0];
			$vars["view"]     = $this->view("bookmark", TRUE);
			
			$this->render("content", $vars);
		} else {
			redirect();
		}
	}
	
	public function visit($bookmarkID = 0) {
		$data = $this->Cache->data("bookmark-$bookmarkID", "bookmarks", $this->Bookmarks_Model, "getByID", array($bookmarkID));

		if($data) {
			$this->Bookmarks_Model->updateViews($bookmarkID);

			redirect($data[0]["URL"]);
		} else {
			redirect();
		}
	}

	public function getBookmarks() {
		$this->title(__("Bookmarks"));
		$this->CSS("bookmarks", $this->application);
		$this->CSS("pagination");
		
		$limit = $this->limit();
		
		$data = $this->Cache->data("bookmarks-$limit", "bookmarks", $this->Bookmarks_Model, "getAll", array($limit));
	
		$this->helper("time");
		
		if($data) {	
			$this->meta("keywords", $data[0]["Tags"]);
			$this->meta("description", $data[0]["Description"]);
                        
			$vars["bookmarks"]  = $data;
			$vars["pagination"] = $this->pagination;
			$vars["view"]       = $this->view("bookmarks", TRUE);
			
			$this->render("content", $vars);
		} else {
			redirect();	
		} 
	}

	public function getBookmarksByAuthor($author) {
		$this->title(__("$author Bookmarks"));
		$this->CSS("bookmarks", $this->application);
		$this->CSS("pagination");
		
		$limit = $this->limit();
		
		$data = $this->Cache->data("bookmarks-$limit", "bookmarks", $this->Bookmarks_Model, "getAllByAuthor", array($author, $limit));
	
		$this->helper("time");
		
		if($data) {	
			$this->meta("keywords", $data[0]["Tags"]);
			$this->meta("description", $data[0]["Description"]);
                        
			$vars["bookmarks"]  = $data;
			$vars["pagination"] = $this->pagination;
			$vars["view"]       = $this->view("bookmarks", TRUE);
			
			$this->render("content", $vars);
		} else {
			redirect();	
		} 
	}

	private function limit($tag = NULL) {
		$count = $this->Bookmarks_Model->count($tag);	
		
		if(is_null($tag)) {
			$start = (segment(1, isLang()) === "page" and segment(2, isLang()) > 0) ? (segment(2, isLang()) * _maxLimit) - _maxLimit : 0;
			$URL   = path("bookmarks/page/");
		} else {
			$start = (segment(3, isLang()) === "page" and segment(4, isLang()) > 0) ? (segment(4, isLang()) * _maxLimit) - _maxLimit : 0;
			$URL   = path("bookmarks/tag/$tag/page/");
		}	

		$limit = $start .", ". _maxLimit;
		
		$this->pagination = ($count > _maxLimit) ? paginate($count, _maxLimit, $start, $URL) : NULL;

		return $limit;
	}
}