<?php

/* Security measure */
if (!defined('IN_CMS')) { exit; }

class LinkTrackerClick extends Record
{
	const TABLE_NAME = 'jb_link_tracker_clicks';
	
	public $link_tracker_id;
	public $ip_address;
	public $user_agent;
	public $referer;
	public $datetime;
	
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
		
		$table = self::tableNameFromClassName('LinkTrackerClick');
		
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
			return $query->fetchObject('LinkTrackerClick');
		}
		else {
			$results = array();
			while ($obj = $query->fetchObject('LinkTrackerClick')) {
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
	 * Find records by tracker id field value.
	 *
	 * @param int $t_id
	 * @param bool|string $where Additional refinement criteria.
	 * @return bool|array
	 */
	public static function findByTrackerId($t_id, $where=false)
	{
		$t_id = (int) $t_id;
		
		return self::find(array(
			'where' => "link_tracker_id = {$t_id}" . (!$where ? '' : " AND $where")
		));
	}
	
	/**
	 * Find records by ip address field value.
	 *
	 * @param string $ip_addr
	 * @param bool|string $where Additional refinement criteria.
	 * @return bool|array
	 */
	public static function findByIpAddr($ip_addr, $where=false)
	{
		$conn = Record::getConnection();
		$ip_addr = $conn->quote($ip_addr);
		
		return self::find(array(
			'where' => "ip_address = $ip_addr" . (!$where ? '' : " AND $where")
		));
	}
	
	/**
	 * Find records by user agent field value.
	 *
	 * @param string $user_agent
	 * @param bool|string $where Additional refinement criteria.
	 * @return bool|array
	 */
	public static function findByUserAgent($user_agent, $where=false)
	{
		$conn = Record::getConnection();
		$user_agent = $conn->quote($user_agent);
		
		return self::find(array(
			'where' => "user_agent = $user_agent" . (!$where ? '' : " AND $where")
		));
	}
	
	/**
	 * Find records by referer field value.
	 *
	 * @param string $referer
	 * @param bool|string $where Additional refinement criteria.
	 * @return bool|array
	 */
	public static function findByReferer($referer, $where=false)
	{
		$conn = Record::getConnection();
		$referer = $conn->quote($referer);
		
		return self::find(array(
			'where' => "referer = $referer" . (!$where ? '' : " AND $where")
		));
	}
	
	/**
	 * Delete specific rows from the table.
	 *
	 * @param array $args Where,limit,offset,order params.
	 * @return bool
	 */
	public static function deleteRows($args=array()) 
	{
		$rows = self::findAll($args);
		if (count($rows) == 0) {
			return false;
		}
		
		foreach ($rows as $k => $tracker) {
			$tracker->delete();
		}
		
		return true;
	}
	
	/**
	 * Find records with a specific date.
	 *
	 * @param string $date
	 * @param bool|string $where
	 * @return bool|array
	 */
	public static function findByDate($date, $where=false)
	{
		if (!is_numeric($date)) { $date = strtotime($date); }
		
		$day = date('j', $date);
		$month = date('n', $date);
		$year = date('Y', $date);
		
		return self::find(array(
			'where' => "
				DAY(datetime) = '$day' AND
				MONTH(datetime) = '$month' AND
				YEAR(datetime) = '$year'" . (!$where ? '' : " AND $where"),
		));
	}

} // End of LinkTrackerClick 