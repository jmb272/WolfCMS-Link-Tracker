<?php

/* Security measure */
if (!defined('IN_CMS')) { exit; }

class LinkTracker extends Record
{
	const TABLE_NAME = 'jb_link_trackers';

	public $name;
	public $url;
	public $click_count;
	public $first_clicked_on;
	public $last_clicked_on;
	
	/**
	 * Select query builder.
	 *
	 * @param array $args
	 * @return bool|array|object
	 */
	public static function find($args = array())
	{
		$where = isset($args['where']) ? trim($args['where']) : '';
		$order = isset($args['order']) ? trim($args['order']) : '';
		$limit = isset($args['limit']) ? (int) $args['limit'] : 0;
		$offset = isset($args['offset']) ? (int) $args['offset'] : 0;
		
		$table = self::tableNameFromClassName('LinkTracker');
		
		$sql = "SELECT * FROM {$table}";
		if (!empty($where)) { $sql .= " WHERE {$where}"; }
		if (!empty($order)) { $sql .= " ORDER BY {$order}"; }
		if (!empty($limit)) { $sql .= " LIMIT {$limit}"; }
		if (!empty($offset)) { $sql .= " OFFSET {$offset}"; }
		
		$query = Record::getConnection()->prepare(trim($sql));
		if (!$query->execute()) {
			return false;
		}
		
		if ($limit == 1) {
			return $query->fetchObject('LinkTracker');
		}
		else {
			$results = array();
			while ($obj = $query->fetchObject('LinkTracker')) {
				$results[] = $obj;
			}
			
			return $results;
		}
	}
	
	/**
	 * Find all records.
	 *
	 * @param array $args
	 * @return bool|array|object
	 */
	public static function findAll($args=array())
	{
		return self::find($args);
	}
	
	/**
	 * Count all records.
	 *
	 * @param array $args
	 * @return int
	 */
	public static function countAll($args=array())
	{
		return (int) count(self::find($args));
	}
	
	/**
	 * Find a record by the ID field.
	 *
	 * @param int $id
	 * @param bool|string $where Additional refinement criteria.
	 * @return bool|object
	 */
	public static function findById($id, $where=false)
	{
		if (!is_numeric($id)) { return false; }
		
		return self::find(array(
			'where' => "id=$id" . (!$where ? '' : " AND $where"),
			'limit' => 1
		));
	}
	
	/**
	 * Find the first record by the ID field.
	 * Optionally, you may pass in additional refinement criteria
	 * to enhance this method's functionality.
	 *
	 * @param bool|string $where
	 * @return bool|object
	 */
	public static function getFirstById($where=false)
	{
		if (!$where) {
			return self::find(array(
				'order' => 'id ASC',
				'limit' => 1
			));
		}
		else {
			return self::find(array(
				'where' => $where,
				'order' => 'id ASC',
				'limit' => 1
			));
		}
	}
	
	/**
	 * Find the last record by the ID field.
	 * Optionally, you may pass in additional refinement criteria
	 * to enhance this method's functionality.
	 *
	 * @param bool|string $where
	 * @return bool|object
	 */
	public static function getLastById($where=false)
	{
		if (!$where) {
			return self::find(array(
				'order' => 'id DESC',
				'limit' => 1
			));
		}
		else {
			return self::find(array(
				'where' => $where,
				'order' => 'id DESC',
				'limit' => 1
			));
		}
	}
	
	/**
	 * Find a record by the name field.
	 *
	 * @param string $name
	 * @param bool|string $where Additional refinement criteria.
	 * @return bool|object
	 */
	public static function findByName($name, $where=false)
	{
		$conn = Record::getConnection();
		$name = $conn->quote($name);
		
		return self::find(array(
			'where' => "name=$name" . (!$where ? '' : " AND $where"),
			'limit' => 1
		));
	}
	
	/**
	 * Return the URL field value for a record.
	 *
	 * @param int $id
	 * @return bool|string
	 */
	public static function getURLbyId($id)
	{
		$tracker = self::findById($id);
		if (!is_object($tracker)) {
			return false;
		}
		
		return $tracker->url;
	}
	
	/*** Instance Methods ***/
	
	/**
	 * Reset the statistics fields of the active tracker object
	 * and deletes the assocated click records.
	 *
	 * @return bool
	 */
	public function reset_stats()
	{
		if (!property_exists($this, 'id')) { return false; }
		
		// Nullify fields.
		$this->click_count = 0;
		$this->first_clicked_on = '0000-00-00 00:00:00';
		$this->last_clicked_on = '0000-00-00 00:00:00';
		
		// Delete associated click records.
		LinkTrackerClick::deleteRows(array('where' => 'link_tracker_id = '.$this->id));
		
		return (bool) $this->save();
	}
	
	/**
	 * Deleting records with this method will also remove
	 * associated data from other tables.
	 *
	 * @return bool
	 */
	public function delete_clean()
	{
		if (!property_exists($this, 'id')) { return false; }
	
		// Delete associated click records.
		LinkTrackerClick::deleteRows(array('where' => 'link_tracker_id = '.$this->id));
		
		return (bool) $this->delete();
	}

} // End of LinkTracker record.
