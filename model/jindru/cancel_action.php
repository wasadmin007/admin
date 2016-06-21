<?php 
class ModelJindruCancelAction extends Model {
	public function addCancelAction($data) {
		foreach ($data['cancel_action'] as $language_id => $value) {
			if (isset($cancel_action_id)) {
				$this->db->query("INSERT INTO " . DB_PREFIX . "cancel_action SET cancel_action_id = '" . (int)$cancel_action_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
			} else {
				$this->db->query("INSERT INTO " . DB_PREFIX . "cancel_action SET language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");

				$cancel_action_id = $this->db->getLastId();
			}
		}

		$this->cache->delete('cancel_action');
	}

	public function editCancelAction($cancel_action_id, $data) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "cancel_action WHERE cancel_action_id = '" . (int)$cancel_action_id . "'");

		foreach ($data['cancel_action'] as $language_id => $value) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "cancel_action SET cancel_action_id = '" . (int)$cancel_action_id . "', language_id = '" . (int)$language_id . "', name = '" . $this->db->escape($value['name']) . "'");
		}

		$this->cache->delete('cancel_action');
	}

	public function deleteCancelAction($cancel_action_id) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "cancel_action WHERE cancel_action_id = '" . (int)$cancel_action_id . "'");

		$this->cache->delete('cancel_action');
	}

	public function getCancelAction($cancel_action_id) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cancel_action WHERE cancel_action_id = '" . (int)$cancel_action_id . "' AND language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	public function getCancelActions($data = array()) {
		if ($data) {
			$sql = "SELECT * FROM " . DB_PREFIX . "cancel_action WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'";

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
			$cancel_action_data = $this->cache->get('cancel_action.' . (int)$this->config->get('config_language_id'));

			if (!$cancel_action_data) {
				$query = $this->db->query("SELECT cancel_action_id, name FROM " . DB_PREFIX . "cancel_action WHERE language_id = '" . (int)$this->config->get('config_language_id') . "' ORDER BY name");

				$cancel_action_data = $query->rows;

				$this->cache->set('cancel_action.' . (int)$this->config->get('config_language_id'), $cancel_action_data);
			}	

			return $cancel_action_data;				
		}
	}

	public function getCancelActionDescriptions($cancel_action_id) {
		$cancel_action_data = array();

		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "cancel_action WHERE cancel_action_id = '" . (int)$cancel_action_id . "'");

		foreach ($query->rows as $result) {
			$cancel_action_data[$result['language_id']] = array('name' => $result['name']);
		}

		return $cancel_action_data;
	}

	public function getTotalCancelActions() {
		$query = $this->db->query("SELECT COUNT(*) AS total FROM " . DB_PREFIX . "cancel_action WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row['total'];
	}	
}
?>