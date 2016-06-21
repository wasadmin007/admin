<?php
class ModelReportCancel extends Model {
	public function getCancels($data = array()) {
		$sql = "SELECT MIN(r.date_added) AS date_start, MAX(r.date_added) AS date_end, COUNT(r.cancel_id) AS `cancels` FROM `" . DB_PREFIX . "cancel` r"; 

		if (!empty($data['filter_cancel_status_id'])) {
			$sql .= " WHERE r.cancel_status_id = '" . (int)$data['filter_cancel_status_id'] . "'";
		} else {
			$sql .= " WHERE r.cancel_status_id > '0'";
		}
		
		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(r.date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(r.date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}
		
		if (isset($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'week';
		}
		
		switch($group) {
			case 'day';
				$sql .= " GROUP BY DAY(r.date_added)";
				break;
			default:
			case 'week':
				$sql .= " GROUP BY WEEK(r.date_added)";
				break;	
			case 'month':
				$sql .= " GROUP BY MONTH(r.date_added)";
				break;
			case 'year':
				$sql .= " GROUP BY YEAR(r.date_added)";
				break;									
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
		if (!empty($data['filter_group'])) {
			$group = $data['filter_group'];
		} else {
			$group = 'week';
		}
		
		switch($group) {
			case 'day';
				$sql = "SELECT COUNT(DISTINCT DAY(date_added)) AS total FROM `" . DB_PREFIX . "cancel`";
				break;
			default:
			case 'week':
				$sql = "SELECT COUNT(DISTINCT WEEK(date_added)) AS total FROM `" . DB_PREFIX . "cancel`";
				break;	
			case 'month':
				$sql = "SELECT COUNT(DISTINCT MONTH(date_added)) AS total FROM `" . DB_PREFIX . "cancel`";
				break;
			case 'year':
				$sql = "SELECT COUNT(DISTINCT YEAR(date_added)) AS total FROM `" . DB_PREFIX . "cancel`";
				break;									
		}
		
		if (!empty($data['filter_cancel_status_id'])) {
			$sql .= " WHERE cancel_status_id = '" . (int)$data['filter_cancel_status_id'] . "'";
		} else {
			$sql .= " WHERE cancel_status_id > '0'";
		}
				
		if (!empty($data['filter_date_start'])) {
			$sql .= " AND DATE(date_added) >= '" . $this->db->escape($data['filter_date_start']) . "'";
		}

		if (!empty($data['filter_date_end'])) {
			$sql .= " AND DATE(date_added) <= '" . $this->db->escape($data['filter_date_end']) . "'";
		}

		$query = $this->db->query($sql);

		return $query->row['total'];	
	}	
}
?>