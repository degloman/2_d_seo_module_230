<?php
class ModelDSEOModuleDSEOModule extends Model {
		
	/*
	*	Add Language.
	*/
	public function addLanguage($data) {
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "manufacturer_description WHERE language_id = '" . (int)$this->config->get('config_language_id') . "'");

		foreach ($query->rows as $manufacturer) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer_description SET manufacturer_id = '" . (int)$manufacturer['manufacturer_id'] . "', language_id = '" . (int)$data['language_id'] . "'");
		}

		$this->cache->delete('manufacturer');
	}
	
	/*
	*	Delete Language.
	*/
	public function deleteLanguage($data) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "manufacturer_description WHERE language_id = '" . (int)$data['language_id'] . "'");

		$this->cache->delete('manufacturer');
	}
	
	/*
	*	Add Manufacturer Description.
	*/
	public function addManufacturerDescription($data) {
		foreach ($data['manufacturer_description'] as $language_id => $manufacturer_description) {
			$this->db->query("INSERT INTO " . DB_PREFIX . "manufacturer_description SET manufacturer_id = '" . (int)$data['manufacturer_id'] . "', language_id = '" . (int)$language_id . "'");
		}
	}
	
	/*
	*	Save Category Target Keyword.
	*/
	public function saveCategoryTargetKeyword($data) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "url_target WHERE route = 'category_id=" . (int)$data['category_id'] . "'");
						
		if (isset($data['target_keyword'])) {
			foreach ($data['target_keyword'] as $language_id => $keywords) {
				$sort_order = 1;
				foreach ($keywords as $keyword) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "url_target SET route = 'category_id=" . (int)$data['category_id'] . "', language_id = '" . (int)$language_id . "', sort_order = '" . $sort_order . "', keyword = '" .  $this->db->escape($keyword) . "'");
					
					$sort_order++;
				}
			}
		}
	}
	
	/*
	*	Save Product Target Keyword.
	*/
	public function saveProductTargetKeyword($data) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "url_target WHERE route = 'product_id=" . (int)$data['product_id'] . "'");
						
		if (isset($data['target_keyword'])) {
			foreach ($data['target_keyword'] as $language_id => $keywords) {
				$sort_order = 1;
				foreach ($keywords as $keyword) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "url_target SET route = 'product_id=" . (int)$data['product_id'] . "', language_id = '" . (int)$language_id . "', sort_order = '" . $sort_order . "', keyword = '" .  $this->db->escape($keyword) . "'");
					
					$sort_order++;
				}
			}
		}
	}
	
	/*
	*	Save Manufacturer Target Keyword.
	*/
	public function saveManufacturerTargetKeyword($data) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "url_target WHERE route = 'manufacturer_id=" . (int)$data['manufacturer_id'] . "'");
						
		if (isset($data['target_keyword'])) {
			foreach ($data['target_keyword'] as $language_id => $keywords) {
				$sort_order = 1;
				foreach ($keywords as $keyword) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "url_target SET route = 'manufacturer_id=" . (int)$data['manufacturer_id'] . "', language_id = '" . (int)$language_id . "', sort_order = '" . $sort_order . "', keyword = '" .  $this->db->escape($keyword) . "'");
					
					$sort_order++;
				}
			}
		}
	}
	
	/*
	*	Save Information Target Keyword.
	*/
	public function saveInformationTargetKeyword($data) {
		$this->db->query("DELETE FROM " . DB_PREFIX . "url_target WHERE route = 'information_id=" . (int)$data['information_id'] . "'");
						
		if (isset($data['target_keyword'])) {
			foreach ($data['target_keyword'] as $language_id => $keywords) {
				$sort_order = 1;
				foreach ($keywords as $keyword) {
					$this->db->query("INSERT INTO " . DB_PREFIX . "url_target SET route = 'information_id=" . (int)$data['information_id'] . "', language_id = '" . (int)$language_id . "', sort_order = '" . $sort_order . "', keyword = '" .  $this->db->escape($keyword) . "'");
					
					$sort_order++;
				}
			}
		}
	}	
		
	/*
	*	Return Target Keywords.
	*/
	public function getTargetKeywords($data = array()) {
		$target_keywords = array();
		
		$languages = $this->getLanguages();
		
		$sql = "SELECT * FROM " . DB_PREFIX . "url_target";
		
		$implode = array();
		
		if (!empty($data['filter_route'])) {
			$implode[] = "route = '" . $this->db->escape($data['filter_route']) . "'";
		}
		
		if (!empty($data['filter_language_id'])) {
			$implode[] = "language_id = '" . (int)$data['filter_language_id'] . "'";
		}
		
		if (!empty($data['filter_sort_order'])) {
			$implode[] = "sort_order = '" . (int)$data['filter_sort_order'] . "'";
		}
		
		if (!empty($data['filter_keyword'])) {
			$implode[] = "keyword = '" . $this->db->escape($data['filter_keyword']) . "'";
		}
		
		if ($implode) {
			$sql .= " WHERE " . implode(' AND ', $implode);
		}
		
		$sql .= " ORDER BY sort_order";
				
		$query = $this->db->query($sql);
		
		foreach ($query->rows as $result) {
			if ($result['language_id'] && $result['sort_order'] && $result['keyword']) {
				$target_keywords[$result['route']][$result['language_id']][$result['sort_order']] = $result['keyword'];
			}
		}
								
		return $target_keywords;
	}
	
	/*
	*	Return Category Target Keyword.
	*/
	public function getCategoryTargetKeyword($category_id) {
		$category_target_keyword = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_target WHERE route = 'category_id=" . (int)$category_id . "' ORDER BY sort_order");
		
		foreach($query->rows as $result) {
			$category_target_keyword[$result['language_id']][$result['sort_order']]['value'] = $result['keyword'];
			if (count($this->getTargetKeywords(array('filter_keyword' => $result['keyword'])))>1) {
				$category_target_keyword[$result['language_id']][$result['sort_order']]['duplicate'] = 1;
			}
		}
		
		return $category_target_keyword;
	}
	
	/*
	*	Return Product Target Keyword.
	*/
	public function getProductTargetKeyword($product_id) {
		$product_target_keyword = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_target WHERE route = 'product_id=" . (int)$product_id . "' ORDER BY sort_order");
		
		foreach($query->rows as $result) {
			$product_target_keyword[$result['language_id']][$result['sort_order']]['value'] = $result['keyword'];
			if (count($this->getTargetKeywords(array('filter_keyword' => $result['keyword'])))>1) {
				$product_target_keyword[$result['language_id']][$result['sort_order']]['duplicate'] = 1;
			}
		}
		
		return $product_target_keyword;
	}
	
	/*
	*	Return Manufacturer Target Keyword.
	*/
	public function getManufacturerTargetKeyword($manufacturer_id) {
		$manufacturer_target_keyword = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_target WHERE route = 'manufacturer_id=" . (int)$manufacturer_id . "' ORDER BY sort_order");
		
		foreach($query->rows as $result) {
			$manufacturer_target_keyword[$result['language_id']][$result['sort_order']]['value'] = $result['keyword'];
			if (count($this->getTargetKeywords(array('filter_keyword' => $result['keyword'])))>1) {
				$manufacturer_target_keyword[$result['language_id']][$result['sort_order']]['duplicate'] = 1;
			}
		}
		
		return $manufacturer_target_keyword;
	}
	
	/*
	*	Return Information Target Keyword.
	*/
	public function getInformationTargetKeyword($information_id) {
		$information_target_keyword = array();
		
		$query = $this->db->query("SELECT * FROM " . DB_PREFIX . "url_target WHERE route = 'information_id=" . (int)$information_id . "' ORDER BY sort_order");
		
		foreach($query->rows as $result) {
			$information_target_keyword[$result['language_id']][$result['sort_order']]['value'] = $result['keyword'];
			if (count($this->getTargetKeywords(array('filter_keyword' => $result['keyword'])))>1) {
				$information_target_keyword[$result['language_id']][$result['sort_order']]['duplicate'] = 1;
			}
		}
		
		return $information_target_keyword;
	}
		
	/*
	*	Return list of languages.
	*/
	public function getLanguages() {
		$this->load->model('localisation/language');
		
		$languages = $this->model_localisation_language->getLanguages();
		foreach ($languages as $key => $language) {
            $languages[$key]['flag'] = 'language/' . $language['code'] . '/' . $language['code'] . '.png';
        }
		
		return $languages;
	}
}
?>