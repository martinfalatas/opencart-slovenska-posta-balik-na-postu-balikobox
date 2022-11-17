<?php
class ControllerExtensionShippingSlovakPostAddress extends Controller {
	private $error = array();

	public function index() {
		$this->load->language('extension/shipping/slovak_post_address');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('setting/setting');

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
			$this->model_setting_setting->editSetting('slovak_post_address', $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

            $this->response->redirect($this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=shipping', true));
		}

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}


        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/dashboard', 'token=' . $this->session->data['token'], true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_shipping'),
            'href' => $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=shipping', true)
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('heading_title'),
            'href' => $this->url->link('extension/shipping/slovak_post_address', 'token=' . $this->session->data['token'], true)
        );

        $data['action'] = $this->url->link('extension/shipping/slovak_post_address', 'token=' . $this->session->data['token'], true);

        $data['cancel'] = $this->url->link('extension/extension', 'token=' . $this->session->data['token'] . '&type=shipping', true);



        $data['heading_title'] = $this->language->get('heading_title');
        $data['text_edit'] = $this->language->get('text_edit');

        $data['text_enabled'] = $this->language->get('text_enabled');
        $data['text_disabled'] = $this->language->get('text_disabled');
        $data['text_yes'] = $this->language->get('text_yes');
        $data['text_no'] = $this->language->get('text_no');
        $data['text_all_zones'] = $this->language->get('text_all_zones');
        $data['text_none'] = $this->language->get('text_none');

        $data['entry_total'] = $this->language->get('entry_total');
        $data['entry_sub_total'] = $this->language->get('entry_sub_total');
        $data['entry_rate'] = $this->language->get('entry_rate');
        $data['entry_rate_placeholder'] = $this->language->get('entry_rate_placeholder');
        $data['entry_tax_class'] = $this->language->get('entry_tax_class');
        $data['entry_geo_zone'] = $this->language->get('entry_geo_zone');
        $data['entry_status'] = $this->language->get('entry_status');
        $data['entry_sort_order'] = $this->language->get('entry_sort_order');
        $data['entry_weight_display'] = $this->language->get('entry_weight_display');
        $data['entry_url'] = $this->language->get('entry_url');
        $data['help_total'] = $this->language->get('help_total');
        $data['help_rate'] = $this->language->get('help_rate');
        $data['help_url'] = $this->language->get('help_url');

        $data['button_save'] = $this->language->get('button_save');
        $data['button_cancel'] = $this->language->get('button_cancel');



        if (isset($this->request->post['slovak_post_address_url'])) {
            $data['slovak_post_address_url'] = $this->request->post['slovak_post_address_url'];
        } else {
            $data['slovak_post_address_url'] = $this->config->get('slovak_post_address_url');
        }

        if (isset($this->request->post['slovak_post_address_total'])) {
            $data['slovak_post_address_total'] = $this->request->post['slovak_post_address_total'];
        } elseif ($this->config->get('slovak_post_address_total')) {
            $data['slovak_post_address_total'] = $this->config->get('slovak_post_address_total');
        } else {
            $data['slovak_post_address_total'] = '0';
        }


        if (isset($this->request->post['slovak_post_address_rate'])) {
            $data['slovak_post_address_rate'] = $this->request->post['slovak_post_address_rate'];
        } elseif ($this->config->get('slovak_post_address_rate')) {
            $data['slovak_post_address_rate'] = $this->config->get('slovak_post_address_rate');
        } else {
            $data['slovak_post_address_rate'] = '1:1.5,5:3,20:5.5';
        }

        if (isset($this->request->post['slovak_post_address_weight_display'])) {
            $data['slovak_post_address_weight_display'] = $this->request->post['slovak_post_address_weight_display'];
        } else {
            $data['slovak_post_address_weight_display'] = $this->config->get('slovak_post_address_weight_display');
        }

		if (isset($this->request->post['slovak_post_address_tax_class_id'])) {
			$data['slovak_post_address_tax_class_id'] = $this->request->post['slovak_post_address_tax_class_id'];
		} else {
			$data['slovak_post_address_tax_class_id'] = $this->config->get('slovak_post_address_tax_class_id');
		}

		$this->load->model('localisation/tax_class');

		$data['tax_classes'] = $this->model_localisation_tax_class->getTaxClasses();

		if (isset($this->request->post['slovak_post_address_geo_zone_id'])) {
			$data['slovak_post_address_geo_zone_id'] = $this->request->post['slovak_post_address_geo_zone_id'];
		} else {
			$data['slovak_post_address_geo_zone_id'] = $this->config->get('slovak_post_address_geo_zone_id');
		}

		$this->load->model('localisation/geo_zone');

		$data['geo_zones'] = $this->model_localisation_geo_zone->getGeoZones();

		if (isset($this->request->post['slovak_post_address_status'])) {
			$data['slovak_post_address_status'] = $this->request->post['slovak_post_address_status'];
		} else {
			$data['slovak_post_address_status'] = $this->config->get('slovak_post_address_status');
		}

		if (isset($this->request->post['slovak_post_address_sort_order'])) {
			$data['slovak_post_address_sort_order'] = $this->request->post['slovak_post_address_sort_order'];
		} else {
			$data['slovak_post_address_sort_order'] = $this->config->get('slovak_post_address_sort_order');
		}

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');


		$this->response->setOutput($this->load->view('extension/shipping/slovak_post_address', $data));
	}

	protected function validate() {
		if (!$this->user->hasPermission('modify', 'extension/shipping/slovak_post_address')) {
			$this->error['warning'] = $this->language->get('error_permission');
		}

        $pattern = '/^(\d+([.]\d+)?)(:)(\d+([.]\d+)?)(([,])(\d+([.]\d+)?)(:)(\d+([.]\d+)?))*$/';
        if ((!preg_match( $pattern , $this->request->post['slovak_post_address_rate'])) ) {
            $this->error['warning'] = $this->language->get('error_rate');
        }

		return !$this->error;
	}
}