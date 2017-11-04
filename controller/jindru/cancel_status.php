<?php 
class ControllerJindruCancelStatus extends Controller { 
	private $error = array();

	public function index() {
		$this->language->load('jindru/cancel_status');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('jindru/cancel_status');

		$this->getList();
	}

	public function insert() {
		$this->language->load('jindru/cancel_status');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('jindru/cancel_status');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_jindru_cancel_status->addCancelStatus($this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->redirect($this->url->link('jindru/cancel_status', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function update() {
		$this->language->load('jindru/cancel_status');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('jindru/cancel_status');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {
			$this->model_jindru_cancel_status->editCancelStatus($this->request->get['cancel_status_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->redirect($this->url->link('jindru/cancel_status', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getForm();
	}

	public function delete() {
		$this->language->load('jindru/cancel_status');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('jindru/cancel_status');

		if (isset($this->request->post['selected']) && $this->validateDelete()) {
			foreach ($this->request->post['selected'] as $cancel_status_id) {
				$this->model_jindru_cancel_status->deleteCancelStatus($cancel_status_id);
			}

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->redirect($this->url->link('jindru/cancel_status', 'token=' . $this->session->data['token'] . $url, 'SSL'));
		}

		$this->getList();
	}

	protected function getList() {
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'name';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$this->data['breadcrumbs'] = array();

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => false
		);

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('jindru/cancel_status', 'token=' . $this->session->data['token'] . $url, 'SSL'),
			'separator' => ' :: '
		);

		$this->data['insert'] = $this->url->link('jindru/cancel_status/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		$this->data['delete'] = $this->url->link('jindru/cancel_status/delete', 'token=' . $this->session->data['token'] . $url, 'SSL');	

		$this->data['cancel_statuses'] = array();

		$data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_admin_limit'),
			'limit' => $this->config->get('config_admin_limit')
		);

		$cancel_status_total = $this->model_jindru_cancel_status->getTotalCancelStatuses();

		$results = $this->model_jindru_cancel_status->getCancelStatuses($data);

		foreach ($results as $result) {
			$action = array();

			$action[] = array(
				'text' => $this->language->get('text_edit'),
				'href' => $this->url->link('jindru/cancel_status/update', 'token=' . $this->session->data['token'] . '&cancel_status_id=' . $result['cancel_status_id'] . $url, 'SSL')
			);

			$this->data['cancel_statuses'][] = array(
				'cancel_status_id' => $result['cancel_status_id'],
				'name'          => $result['name'] . (($result['cancel_status_id'] == $this->config->get('config_cancel_status_id')) ? $this->language->get('text_default') : null),
				'selected'      => isset($this->request->post['selected']) && in_array($result['cancel_status_id'], $this->request->post['selected']),
				'action'        => $action
			);
		}	

		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['text_no_results'] = $this->language->get('text_no_results');

		$this->data['column_name'] = $this->language->get('column_name');
		$this->data['column_action'] = $this->language->get('column_action');		

		$this->data['button_insert'] = $this->language->get('button_insert');
		$this->data['button_delete'] = $this->language->get('button_delete');

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$this->data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$this->data['success'] = '';
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$this->data['sort_name'] = $this->url->link('jindru/cancel_status', 'token=' . $this->session->data['token'] . '&sort=name' . $url, 'SSL');

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $cancel_status_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_admin_limit');
		$pagination->text = $this->language->get('text_pagination');
		$pagination->url = $this->url->link('jindru/cancel_status', 'token=' . $this->session->data['token'] . $url . '&page={page}', 'SSL');

		$this->data['pagination'] = $pagination->render();

		$this->data['sort'] = $sort;
		$this->data['order'] = $order;

		$this->template = 'jindru/cancel_status_list.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());
	}

	protected function getForm() {
		$this->data['heading_title'] = $this->language->get('heading_title');

		$this->data['entry_name'] = $this->language->get('entry_name');

		$this->data['button_save'] = $this->language->get('button_save');
		$this->data['button_cancel'] = $this->language->get('button_cancel');

		if (isset($this->error['warning'])) {
			$this->data['error_warning'] = $this->error['warning'];
		} else {
			$this->data['error_warning'] = '';
		}

		if (isset($this->error['name'])) {
			$this->data['error_name'] = $this->error['name'];
		} else {
			$this->data['error_name'] = array();
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$this->data['breadcrumbs'] = array();

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('text_home'),
			'href'      => $this->url->link('common/home', 'token=' . $this->session->data['token'], 'SSL'),
			'separator' => false
		);

		$this->data['breadcrumbs'][] = array(
			'text'      => $this->language->get('heading_title'),
			'href'      => $this->url->link('jindru/cancel_status', 'token=' . $this->session->data['token'] . $url, 'SSL'),
			'separator' => ' :: '
		);

		if (!isset($this->request->get['cancel_status_id'])) {
			$this->data['action'] = $this->url->link('jindru/cancel_status/insert', 'token=' . $this->session->data['token'] . $url, 'SSL');
		} else {
			$this->data['action'] = $this->url->link('jindru/cancel_status/update', 'token=' . $this->session->data['token'] . '&cancel_status_id=' . $this->request->get['cancel_status_id'] . $url, 'SSL');
		}

		$this->data['cancel'] = $this->url->link('jindru/cancel_status', 'token=' . $this->session->data['token'] . $url, 'SSL');

		$this->load->model('localisation/language');

		$this->data['languages'] = $this->model_localisation_language->getLanguages();

		if (isset($this->request->post['cancel_status'])) {
			$this->data['cancel_status'] = $this->request->post['cancel_status'];
		} elseif (isset($this->request->get['cancel_status_id'])) {
			$this->data['cancel_status'] = $this->model_jindru_cancel_status->getCancelStatusDescriptions($this->request->get['cancel_status_id']);
		} else {
			$this->data['cancel_status'] = array();
		}

		$this->template = 'jindru/cancel_status_form.tpl';
		$this->children = array(
			'common/header',
			'common/footer'
		);

		$this->response->setOutput($this->render());	
	}

	protected function validateForm() {
		if (!$this->user->hasPermission('modify', 'jindru/cancel_status')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		foreach ($this->request->post['cancel_status'] as $language_id => $value) {
			if ((utf8_strlen($value['name']) < 3) || (utf8_strlen($value['name']) > 32)) {
				$this->error['name'][$language_id] = $this->language->get('error_name');
			}
		}

		if (!$this->error) {
			return true;
		} else {
			return false;
		}
	}

	protected function validateDelete() {
		if (!$this->user->hasPermission('modify', 'jindru/cancel_status')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

		$this->load->model('sale/cancel');

		foreach ($this->request->post['selected'] as $cancel_status_id) {
			if ($this->config->get('config_cancel_status_id') == $cancel_status_id) {
				$this->error['warning'] = $this->language->get('error_default');
			}  

			$cancel_total = $this->model_sale_cancel->getTotalCancelsByCancelStatusId($cancel_status_id);

			if ($cancel_total) {
				$this->error['warning'] = sprintf($this->language->get('error_cancel'), $cancel_total);
			}  

			$cancel_total = $this->model_sale_cancel->getTotalCancelHistoriesByCancelStatusId($cancel_status_id);

			if ($cancel_total) {
				$this->error['warning'] = sprintf($this->language->get('error_cancel'), $cancel_total);
			}  			
		}

		if (!$this->error) { 
			return true;
		} else {
			return false;
		}
	}
}
?>