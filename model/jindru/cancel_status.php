<?php 
class ModelJindruCancelStatus extends Model {
	public function addCancelStatus($data) {
		foreach ($data['cancel_status'] as $language_id => $value) {
			if (isset($cancel_status_id)) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "cancel_status SET cancel_status_id = '" . (int)$cancel_status_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
			} else {
				$this->db->query("INSERT INTO " . DB_PREFIX . "cancel_status SET language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");

				$cancel_status_id = $this->db->getLastId();
			}
		}

		$this->cache->delete('cancel_status');
	}

	public function editCancelStatus($cancel_status_id, $data) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "cancel_status WHERE cancel_status_id = '" . (int)$cancel_status_id . "'");

		foreach ($data['cancel_status'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "cancel_status SET cancel_status_id = '" . (int)$cancel_status_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
		}

		$this->cache->delete('cancel_status');
	}

	public function deleteCancelStatus($cancel_status_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "cancel_status WHERE cancel_status_id = '" . (int)$cancel_status_id . "'");

		$this->cache->delete('cancel_status');
	}

	public function getCancelStatus($cancel_status_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cancel_status WHERE cancel_status_id = '" . (int)$cancel_status_id . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getCancelStatuses($data = array()) {
		if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "cancel_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'";

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
			$cancel_status_data = $this->cache->get('cancel_status.' . (int)$this->config->get('config_language_id'));

			if (!$cancel_status_data) {
				$query = $this->db->query("SELECT cancel_status_id, name FROM " . DB_PREFIX . "cancel_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY name");

				$cancel_status_data = $query->rows;

				$this->cache->set('cancel_status.' . (int)$this->config->get('config_language_id'), $cancel_status_data);
			}	

			return $cancel_status_data;				
		}
	}

	public function getCancelStatusDescriptions($cancel_status_id) {
		$cancel_status_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cancel_status WHERE cancel_status_id = '" . (int)$cancel_status_id . "'");

		foreach ($query->rows as $result) {
			$cancel_status_data[$result['language_id']] = array('name' => $result['name']);
		}

		return $cancel_status_data;
	}

	public function getTotalCancelStatuses() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "cancel_status WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row['total'];
	}	
}
?>