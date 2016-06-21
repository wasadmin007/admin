<?php 
class ModelJindruCancelReason extends Model {
	public function addCancelReason($data) {
		foreach ($data['cancel_reason'] as $language_id => $value) {
			if (isset($cancel_reason_id)) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "cancel_reason SET cancel_reason_id = '" . (int)$cancel_reason_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
			} else {
				$this->db->query("INSERT INTO " . DB_PREFIX . "cancel_reason SET language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");

				$cancel_reason_id = $this->db->getLastId();
			}
		}

		$this->cache->delete('cancel_reason');
	}

	public function editCancelReason($cancel_reason_id, $data) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "cancel_reason WHERE cancel_reason_id = '" . (int)$cancel_reason_id . "'");

		foreach ($data['cancel_reason'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "cancel_reason SET cancel_reason_id = '" . (int)$cancel_reason_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
		}

		$this->cache->delete('cancel_reason');
	}

	public function deleteCancelReason($cancel_reason_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "cancel_reason WHERE cancel_reason_id = '" . (int)$cancel_reason_id . "'");

		$this->cache->delete('cancel_reason');
	}

	public function getCancelReason($cancel_reason_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cancel_reason WHERE cancel_reason_id = '" . (int)$cancel_reason_id . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getCancelReasons($data = array()) {
		if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "cancel_reason WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'";

			$sql .= " ORDER BY name";	

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
		} else {
			$cancel_reason_data = $this->cache->get('cancel_reason.' . (int)$this->config->get('config_language_id'));

			if (!$cancel_reason_data) {
				$query = $this->db->query("SELECT cancel_reason_id, name FROM " . DB_PREFIX . "cancel_reason WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY name");

				$cancel_reason_data = $query->rows;

				$this->cache->set('cancel_reason.' . (int)$this->config->get('config_language_id'), $cancel_reason_data);
			}	

			return $cancel_reason_data;				
		}
	}

	public function getCancelReasonDescriptions($cancel_reason_id) {
		$cancel_reason_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cancel_reason WHERE cancel_reason_id = '" . (int)$cancel_reason_id . "'");

		foreach ($query->rows as $result) {
			$cancel_reason_data[$result['language_id']] = array('name' => $result['name']);
		}

		return $cancel_reason_data;
	}

	public function getTotalCancelReasons() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "cancel_reason WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row['total'];
	}	
}
?>