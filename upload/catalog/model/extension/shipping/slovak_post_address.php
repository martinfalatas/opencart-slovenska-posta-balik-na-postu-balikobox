<?php
class ModelExtensionShippingSlovakPostAddress extends Model {

	function getQuote($address) {
		$this->load->language('extension/shipping/slovak_post_address');

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "zone_to_geo_zone WHERE geo_zone_id = '" . (int)$this->config->get('slovak_post_address_geo_zone_id') . "' AND country_id = '" . (int)$address['country_id'] . "' AND (zone_id = '" . (int)$address['zone_id'] . "' OR zone_id = '0')");


        if (!$this->config->get('slovak_post_address_geo_zone_id')) {
            $status = true;
        } elseif ($query->num_rows) {
            $status = true;
        } else {
            $status = false;
        }

        if ($this->cart->getSubTotal() < $this->config->get('slovak_post_address_total')) {
            $status = false;
        }


		$method_data = array();

		if ($status) {

            $cost = 0;
            $weight = $this->cart->getWeight();

            $rates = explode(',', $this->config->get('slovak_post_address_rate'));

            foreach ($rates as $rate) {
                $data = explode(':', $rate);

                if ($data[0] >= $weight) {
                    if (isset($data[1])) {
                        $cost = $data[1];
                    }

                    break;
                }
            }




            $quote_data = array();
            if ((float)$cost) {

                $title = $this->language->get('text_description');
                if( $this->config->get('slovak_post_address_weight_display') ){
                    $title .= '  (' . $this->language->get('text_weight') . ' ' . $this->weight->format($weight, $this->config->get('config_weight_class_id')) . ')';
                }


                $quote_data['slovak_post_address'] = array(
                    'code' => 'slovak_post_address.slovak_post_address',
                    'title'        => $title,
                    'cost' => $cost,
                    'tax_class_id' => $this->config->get('slovak_post_address_tax_class_id'),
                    'text' => $this->currency->format($this->tax->calculate($cost, $this->config->get('slovak_post_address_tax_class_id'), $this->config->get('config_tax')), $this->session->data['currency']),
                );


                $method_data = array(
                    'code'       => 'slovak_post_address',
                    'title'      => $this->language->get('text_title'),
                    'quote'      => $quote_data,
                    'sort_order' => $this->config->get('slovak_post_address_sort_order'),
                    'error'      => false
                );

            }

		}

		return $method_data;
	}
}