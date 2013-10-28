<?php

App::uses('MultitermAppController', 'Multiterm.Controller');

class MultitermsController extends MultitermAppController {

/**
 * Controller name
 *
 * @var string
 * @access public
 */
	public $name = 'Multiterm';

/**
 * Nodes for multiple terms
 *
 * @return void
 */
	public function view() {

		if (isset($this->request->params['slugs'])) {
			$this->request->params['named']['slugs'] = $this->request->params['slugs'];
		}
		
		if (!isset($this->request->params['named']['slugs'])) {
			$this->Session->setFlash(__('Missing terms in url'), 'default', array('class' => 'error'));
			return $this->redirect('/');			
		}	

		$delimiter = Configure::read('Multiterm.delimiter');
		if (is_null($delimiter)) $delimiter = ',';
		$terms = explode($delimiter, $this->request->params['named']['slugs']);

		$params['order'] = 'Node.created DESC';
		$params['limit'] = Configure::read('Reading.nodes_per_page');
		$params['conditions'] = array(
			'OR' => array(
				'Node.visibility_roles' => '',
				'Node.visibility_roles LIKE' => '%"' . $this->Croogo->roleId . '"%',
			),
			'Node.status' => 1,
		);
		foreach ($terms as $term) {
			$params['conditions']['AND']['OR'][] = array('Node.terms LIKE' => '%"'.$term.'"%');
		}	

		if (isset($this->request->params['named']['type'])) {
			$params['conditions']['Node.type'] = $this->request->params['named']['type'];
		}			
		$params['contain'] = array(
			'Meta',
			'Taxonomy' => array(
				'Term',
				'Vocabulary',
			),
			'User',
		);		

		$this->paginate['Node'] = $params;

		if ($this->usePaginationCache) {
			$cacheNamePrefix = 'nodes_multiterm_' . $this->Croogo->roleId . '_' . Inflector::slug($this->request->params['named']['slugs']) . '_' . Configure::read('Config.language');
			$cacheNamePrefix .= isset($this->request->params['named']['type']) ? '_' . $this->request->params['named']['type'] : '';
			
			$this->paginate['page'] = isset($this->request->params['named']['page']) ? $this->params['named']['page'] : 1;
			
			$cacheName = $cacheNamePrefix . '_' . $this->paginate['page'] . '_' . $this->paginate['Node']['limit'];
			$cacheNamePaging = $cacheNamePrefix . '_' . $this->paginate['page'] . '_' . $this->paginate['Node']['limit'] . '_paging';
			$cacheConfig = 'nodes_term';
			$nodes = Cache::read($cacheName, $cacheConfig);			
			if (!$nodes) {
				$nodes = $this->paginate('Node');
				Cache::write($cacheName, $nodes, $cacheConfig);
				Cache::write($cacheNamePaging, $this->request->params['paging'], $cacheConfig);
			} else {
				$paging = Cache::read($cacheNamePaging, $cacheConfig);
				$this->request->params['paging'] = $paging;
				$this->helpers[] = 'Paginator';
			}
		} else {
			$nodes = $this->paginate('Node');
		}

		$this->set(compact('nodes', 'terms'));
		$this->set('title_for_layout', __('Multiple terms'));

		if (isset($this->request->params['named']['type'])) {
			$this->_viewFallback(array(
				'view_' . $this->request->params['named']['type'],
			));		
		}
	}

/**
 * View Fallback
 *
 * @param mixed $views
 * @return string
 * @access protected
 */
	protected function _viewFallback($views) {
		if (is_string($views)) {
			$views = array($views);
		}

		if ($this->theme) {
			$viewPaths = App::path('View');
			foreach ($views as $view) {
				foreach ($viewPaths as $viewPath) {
					$viewPath = $viewPath . 'Themed' . DS . $this->theme . DS . $this->name . DS . $view . $this->ext;
					if (file_exists($viewPath)) {
						return $this->render($view);
					}
				}
			}
		}
	}	

}
