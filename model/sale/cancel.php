<?php
class ModelSaleCancel extends Model {
	public function addCancel($data) {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "cancel` SET order_id = '" . (int)$data['order_id'] . "', product_id = '" . (int)$data['product_id'] . "', customer_id = '" . (int)$data['customer_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', product = '" . $this->db->escape($data['product']) . "', model = '" . $this->db->escape($data['model']) . "', quantity = '" . (int)$data['quantity'] . "', opened = '" . (int)$data['opened'] . "', cancel_reason_id = '" . (int)$data['cancel_reason_id'] . "', cancel_action_id = '" . (int)$data['cancel_action_id'] . "', cancel_status_id = '" . (int)$data['cancel_status_id'] . "', comment = '" . $this->db->escape($data['comment']) . "', date_ordered = '" . $this->db->escape($data['date_ordered']) . "', date_added = NOW(), date_modified = NOW()");
	}

	public function editCancel($cancel_id, $data) {
		$this->db->query("UPDATE `" . DB_PREFIX . "cancel` SET order_id = '" . (int)$data['order_id'] . "', product_id = '" . (int)$data['product_id'] . "', customer_id = '" . (int)$data['customer_id'] . "', firstname = '" . $this->db->escape($data['firstname']) . "', lastname = '" . $this->db->escape($data['lastname']) . "', email = '" . $this->db->escape($data['email']) . "', telephone = '" . $this->db->escape($data['telephone']) . "', product = '" . $this->db->escape($data['product']) . "', model = '" . $this->db->escape($data['model']) . "', quantity = '" . (int)$data['quantity'] . "', opened = '" . (int)$data['opened'] . "', cancel_reason_id = '" . (int)$data['cancel_reason_id'] . "', cancel_action_id = '" . (int)$data['cancel_action_id'] . "', cancel_status_id = '" . (int)$data['cancel_status_id'] . "', comment = '" . $this->db->escape($data['comment']) . "', date_ordered = '" . $this->db->escape($data['date_ordered']) . "', date_modified = NOW() WHERE cancel_id = '" . (int)$cancel_id . "'");
	}

	public function editCancelAction($cancel_id, $cancel_action_id) {
		$this->db->query("UPDATE `" . DB_PREFIX . "cancel` SET cancel_action_id = '" . (int)$cancel_action_id . "' WHERE cancel_id = '" . (int)$cancel_id . "'");
	}

	public function deleteCancel($cancel_id) {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "cancel` WHERE cancel_id = '" . (int)$cancel_id . "'");
		$this->db->query("DELETE FROM " . DB_PREFIX . "cancel_history WHERE cancel_id = '" . (int)$cancel_id . "'");
	}

	public function getCancel($cancel_id) {
		$query = $this->db->query("SELECT DISTINCT *, (SELECT CONCAT(c.firstname, ' ', c.lastname) FROM " . DB_PREFIX . "customer c WHERE c.customer_id = r.customer_id) AS customer FROM `" . DB_PREFIX . "cancel` r WHERE r.cancel_id = '" . (int)$cancel_id . "'");

		return $query->row;
	}

	public function getCancels($data = array()) {
		$sql = "SELECT *, CONCAT(r.firstname, ' ', r.lastname) AS customer, (SELECT rs.name FROM " . DB_PREFIX . "cancel_status rs WHERE rs.cancel_status_id = r.cancel_status_id AND rs.language_id = '" . (int)$this->config->get('config_language_id') . "') AS status FROM `" . DB_PREFIX . "cancel` r";

		$implode = array();

		if (!empty($data['filter_cancel_id'])) {
			$implode[] = "r.cancel_id = '" . (int)$data['filter_cancel_id'] . "'";
		}

		if (!empty($data['filter_order_id'])) {
			$implode[] = "r.order_id = '" . (int)$data['filter_order_id'] . "'";
		}

		if (!empty($data['filter_customer'])) {
			$implode[] = "CONCAT(r.firstname, ' ', r.lastname) LIKE '" . $this->db->escape($data['filter_customer']) . "%'";
		}

		if (!empty($data['filter_product'])) {
			$implode[] = "r.product = '" . $this->db->escape($data['filter_product']) . "'";
		}	

		if (!empty($data['filter_model'])) {
			$implode[] = "r.model = '" . $this->db->escape($data['filter_model']) . "'";
		}	

		if (!empty($data['filter_cancel_status_id'])) {
			$implode[] = "r.cancel_status_id = '" . (int)$data['filter_cancel_status_id'] . "'";
		}	

		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(r.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if (!empty($data['filter_date_modified'])) {
			$implode[] = "DATE(r.date_modified) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$sort_data = array(
			'r.cancel_id',
			'r.order_id',
			'customer',
			'r.product',
			'r.model',
			'status',
			'r.date_added',
			'r.date_modified'
		);	

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];	
		} else {
			$sql .= " ORDER BY r.cancel_id";	
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}			

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}	

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}		

		$query = $this->db->query($sql);

		return $query->rows;	
	}

	public function getTotalCancels($data = array()) {
		$sql = "SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "cancel`r";

		$implode = array();

		if (!empty($data['filter_cancel_id'])) {
			$implode[] = "r.cancel_id = '" . (int)$data['filter_cancel_id'] . "'";
		}

		if (!empty($data['filter_customer'])) {
			$implode[] = "CONCAT(r.firstname, ' ', r.lastname) LIKE '" . $this->db->escape($data['filter_customer']) . "%'";
		}

		if (!empty($data['filter_order_id'])) {
			$implode[] = "r.order_id = '" . $this->db->escape($data['filter_order_id']) . "'";
		}

		if (!empty($data['filter_product'])) {
			$implode[] = "r.product = '" . $this->db->escape($data['filter_product']) . "'";
		}	

		if (!empty($data['filter_model'])) {
			$implode[] = "r.model = '" . $this->db->escape($data['filter_model']) . "'";
		}	

		if (!empty($data['filter_cancel_status_id'])) {
			$implode[] = "r.cancel_status_id = '" . (int)$data['filter_cancel_status_id'] . "'";
		}	

		if (!empty($data['filter_date_added'])) {
			$implode[] = "DATE(r.date_added) = DATE('" . $this->db->escape($data['filter_date_added']) . "')";
		}

		if (!empty($data['filter_date_modified'])) {
			$implode[] = "DATE(r.date_modified) = DATE('" . $this->db->escape($data['filter_date_modified']) . "')";
		}

		if ($implode) {
			$sql .= " WHERE " . implode(" AND ", $implode);
		}

		$query = $this->db->query($sql);

		return $query->row['total'];
	}

	public function getTotalCancelsByCancelStatusId($cancel_status_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "cancel` WHERE cancel_status_id = '" . (int)$cancel_status_id . "'");

		return $query->row['total'];
	}	

	public function getTotalCancelsByCancelReasonId($cancel_reason_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "cancel` WHERE cancel_reason_id = '" . (int)$cancel_reason_id . "'");

		return $query->row['total'];
	}	

	public function getTotalCancelsByCancelActionId($cancel_action_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM `" . DB_PREFIX . "cancel` WHERE cancel_action_id = '" . (int)$cancel_action_id . "'");

		return $query->row['total'];
	}	

	public function addCancelHistory($cancel_id, $data) {
		$this->db->query("UPDATE `" . DB_PREFIX . "cancel` SET cancel_status_id = '" . (int)$data['cancel_status_id'] . "', date_modified = NOW() WHERE cancel_id = '" . (int)$cancel_id . "'");

		$this->db->query("INSERT INTO " . DB_PREFIX . "cancel_history SET cancel_id = '" . (int)$cancel_id . "', cancel_status_id = '" . (int)$data['cancel_status_id'] . "', notify = '" . (isset($data['notify']) ? (int)$data['notify'] : 0) . "', comment = '" . $this->db->escape(strip_tags($data['comment'])) . "', date_added = NOW()");

		if ($data['notify']) {
			$cancel_query = $this->db->query("SELECT *, rs.name AS status FROM `" . DB_PREFIX . "cancel` r LEFT JOIN " . DB_PREFIX . "cancel_status rs ON (r.cancel_status_id = rs.cancel_status_id) WHERE r.cancel_id = '" . (int)$cancel_id . "' AND rs.language_id = '" . (int)$this->config->get('config_language_id') . "'");

			if ($cancel_query->num_rows) {
				$this->language->load('mail/cancel');

				$subject = sprintf($this->language->get('text_subject'), $this->config->get('config_name'), $cancel_id);

				$message  = $this->language->get('text_cancel_id') . ' ' . $cancel_id . "\n";
				$message .= $this->language->get('text_date_added') . ' ' . date($this->language->get('date_format_short'), strtotime($cancel_query->row['date_added'])) . "\n\n";
				$message .= $this->language->get('text_cancel_status') . "\n";
				$message .= $cancel_query->row['status'] . "\n\n";

				if ($data['comment']) {
					$message .= $this->language->get('text_comment') . "\n\n";
					$message .= strip_tags(html_entity_decode($data['comment'], ENT_QUOTES, 'UTF-8')) . "\n\n";
				}

				$message .= $this->language->get('text_footer');

				$mail = new Mail();
				$mail->protocol = $this->config->get('config_mail_protocol');
				$mail->parameter = $this->config->get('config_mail_parameter');
				$mail->hostname = $this->config->get('config_smtp_host');
				$mail->username = $this->config->get('config_smtp_username');
				$mail->password = $this->config->get('config_smtp_password');
				$mail->port = $this->config->get('config_smtp_port');
				$mail->timeout = $this->config->get('config_smtp_timeout');
				$mail->setTo($cancel_query->row['email']);
				$mail->setFrom($this->config->get('config_email'));
				$mail->setSender($this->config->get('config_name'));
				$mail->setSubject(html_entity_decode($subject, ENT_QUOTES, 'UTF-8'));
				$mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
				$mail->send();
			}
		}
	}

	public function getCancelHistories($cancel_id, $start = 0, $limit = 10) {
		if ($start < 0) {
			$start = 0;
		}

		if ($limit < 1) {
			$limit = 10;
		}	

		$query = $this->db->query("SELECT rh.date_added, rs.name AS status, rh.comment, rh.notify FROM " . DB_PREFIX . "cancel_history rh LEFT JOIN " . DB_PREFIX . "cancel_status rs ON rh.cancel_status_id = rs.cancel_status_id WHERE rh.cancel_id = '" . (int)$cancel_id . "' AND rs.language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY rh.date_added ASC LIMIT " . (int)$start . "," . (int)$limit);

		return $query->rows;
	}	

	public function getTotalCancelHistories($cancel_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "cancel_history WHERE cancel_id = '" . (int)$cancel_id . "'");

		return $query->row['total'];
	}	

	public function getTotalCancelHistoriesByCancelStatusId($cancel_status_id) {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "cancel_history WHERE cancel_status_id = '" . (int)$cancel_status_id . "' GROUP BY cancel_id");

		return $query->row['total'];
	}			
}
?>