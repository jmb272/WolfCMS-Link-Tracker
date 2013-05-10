<?php

/* Security measure */
if (!defined('IN_CMS')) { exit; }

class LinkTrackerController extends PluginController 
{
	/**
	 * Constructor.
	 */
    public function __construct() 
	{
        $this->setLayout('backend');
        $this->assignToLayout('sidebar', new View('../../plugins/link_tracker/views/sidebar'));
    }

	/**
	 * Index page.
	 */
    public function index($current_page=1) 
	{
		// Call form processor?
		if (get_request_method() == 'POST') {
			exit($this->_index($current_page));
		}
		
		if (!is_numeric($current_page) || $current_page < 1) {
			redirect(get_url('plugin/link_tracker'));
		}
		
		$view_data = array();
		
		// Get sort order.
		$sort_field = Plugin::getSetting('sort_field', 'link_tracker');
		$sort_order = Plugin::getSetting('sort_order', 'link_tracker');
		
		// Pagination.
		$pagination_enabled = (Plugin::getSetting('paginate', 'link_tracker') == '1');
		if ($pagination_enabled) {
			$rows_per_page = (int) Plugin::getSetting('rows_per_page', 'link_tracker');
			$offset = ($rows_per_page * ($current_page-1));
			$total_row_count = LinkTracker::countAll();
			$total_page_count = ceil($total_row_count / $rows_per_page);
			$view_data = array_merge($view_data, array(
				'pg_rows_per_page' => $rows_per_page,
				'pg_current_page' => $current_page,
				'pg_total_row_count' => $total_row_count,
				'pg_total_page_count' => $total_page_count		
			));			
			$trackers = LinkTracker::findAll(array(
				'order' => $sort_field.' '.$sort_order,
				'offset' => $offset,
				'limit' => $rows_per_page
			));
		} else {
			$trackers = LinkTracker::findAll(array(
				'order' => $sort_field.' '.$sort_order,
			));
		}
		
		if (!is_array($trackers)) { 
			$tmp = array($trackers);
			$trackers = $tmp;
		}
		
		$view_data = array_merge($view_data, array(
			'form_action' => get_url('plugin/link_tracker/index/'.$current_page),
			'pagination_enabled' => $pagination_enabled,
			'trackers' => $trackers,
		));
		
		// Display view.
        $this->display('link_tracker/views/index', $view_data);
    }
	
	/**
	 * Process index page form.
	 */
	private function _index($current_page) 
	{
		$tracker_data = isset($_POST['tracker']) ? $_POST['tracker'] : false;
		
		// No records selected.
		if (!$tracker_data) {
			Flash::set('error', 'No records selected.');
			redirect(get_url('plugin/link_tracker/index/'.$current_page));
		}
		
		// Check action
		$action = isset($_POST['action']) ? $_POST['action'] : 'No action';
		$valid_actions = array('No action', 'Delete', 'Reset stats');
		if (!in_array($action, $valid_actions)) {
			Flash::set('error', 'Invalid action selected.');
			redirect(get_url('plugin/link_tracker/index/'.$current_page));
		}
		
		// Perform action on selected records.
		switch ($action) 
		{
			case 'No action':
				redirect(get_url('plugin/link_tracker/index/'.$current_page));
				break;
				
			case 'Delete': 
				$delete_count = 0;
				foreach ($tracker_data as $id => $val) {
					$tracker = LinkTracker::findById($id);
					if (is_object($tracker)) {
						if ($tracker->delete_clean()) {
							$delete_count++;
						}
					}
				}
				Flash::set('success', "$delete_count records deleted.");
				break;
				
			case 'Reset stats': 
				$update_count = 0;
				foreach ($tracker_data as $id => $val) {
					$tracker = LinkTracker::findById($id);
					if (is_object($tracker)) {
						if ($tracker->reset_stats()) {
							$update_count++;
						}
					}
				}
				Flash::set('success', "$update_count records updated.");
				break;
		}
		
		redirect(get_url('plugin/link_tracker/index/'.$current_page));
		
	}

	/**
	 * Track a link. Frontend method only.
	 *
	 * @param int $id
	 */
	public function track($id)
	{
		// No backend access to this method.
		if (defined('CMS_BACKEND')) {
			redirect(get_url('plugin/link_tracker'));
		}
		
		$tracker = LinkTracker::findById((int)$id);
		
		// Record not found.
		if (!is_object($tracker)) {
			redirect(get_url());
		}	
		
		// Check referer?
		$lock_referer = Plugin::getSetting('lock_referer', 'link_tracker');
		$different_referer = false;
		if (!empty($lock_referer)) {
			// Originated from a different place, skip logging.
			if (!has_prefix($_SERVER['HTTP_REFERER'], $lock_referer)) {
				header('Location: '.add_prefix($tracker->url, 'http://'));
				exit;
			}
		}
		
		// Check IP address?
		$lock_ip_address = Plugin::getSetting('lock_ip_address', 'link_tracker');
		if ($lock_ip_address == '1') {
			// Has this user clicked on this link today?
			$user_ip = $_SERVER['REMOTE_ADDR'];
			$records = LinkTrackerClick::findByDate(time(), "link_tracker_id='{$tracker->id}' AND ip_address='{$user_ip}'");
			// If so, skip logging.
			if (!empty($records)) {
				header('Location: '.add_prefix($tracker->url, 'http://'));
				exit;
			}
		}
	
		// First click?
		if ($tracker->click_count == 0) {
			$tracker->first_clicked_on = date('Y-m-d H:i:s');
		}
	
		$tracker->last_clicked_on = date('Y-m-d H:i:s');
		$tracker->click_count++;
		$tracker->save();
		
		// Record tracker click.
		$store_click_details = (Plugin::getSetting('store_click_details', 'link_tracker') == '1');
		if ($store_click_details) 
		{
			$tracker_click = new LinkTrackerClick(array(
				'link_tracker_id' => $tracker->id,
				'ip_address' => $_SERVER['REMOTE_ADDR'],
				'user_agent' => $_SERVER['HTTP_USER_AGENT'],
				'referer' => $_SERVER['HTTP_REFERER'],
				'datetime' => date('Y-m-d H:i:s'),
			));
			$tracker_click->save();
			
			$saved_clicks_per_tracker = (int) Plugin::getSetting('saved_clicks_per_tracker', 'link_tracker');
			if ($saved_clicks_per_tracker > 0) {
				$tracker_clicks = LinkTrackerClick::findByTrackerId($tracker->id);
				$tracker_click_count = count($tracker_clicks);
				if ($tracker_click_count > $saved_clicks_per_tracker) {
					LinkTrackerClick::deleteRows(array(
						'order' => 'datetime ASC',
						'limit' => ($tracker_click_count - $saved_clicks_per_tracker),
					));
				}
			}
		}
		
		header('Location: '.add_prefix($tracker->url, 'http://'));
	
	}
	
	/**
	 * Display the add tracker form.
	 */
	public function add()
	{
		// Process form?
		if (get_request_method() == 'POST') {
			exit($this->_add());
		}
		
		$post_data = Flash::get('post_data');
		if ($post_data) {
			$tracker = $post_data;
		} else {
			$tracker_obj = new LinkTracker;
			$tracker = (array) $tracker_obj;
		}
	
		$this->display('link_tracker/views/edit', array(
			'action' => 'add',
			'form_action' => get_url('plugin/link_tracker/add'),
			'tracker' => $tracker
		));
	}
	
	/**
	 * Process the add tracker form.
	 */
	private function _add() 
	{ 
		// Check post data.
		$post_data_found = (isset($_POST['tracker']) && !empty($_POST['tracker']));
		$post_data = ($post_data_found ? $_POST['tracker'] : false);
		if (!$post_data) {
			Flash::set('error', __('Post data not found.'));
			redirect(get_url('plugin/link_tracker/add'));
		}
		
		// Check valid fields.
		if (!isset($post_data['name']) || empty($post_data['name'])) {
			Flash::set('error', __('Please enter a name.'));
			Flash::set('post_data', $post_data);
			redirect(get_url('plugin/link_tracker/add'));
		}
		if (!isset($post_data['url']) || empty($post_data['url'])) {
			Flash::set('error', __('Please enter a URL.'));
			Flash::set('post_data', $post_data);
			redirect(get_url('plugin/link_tracker/add'));
		}
		
		// Name taken?
		$name_exists = LinkTracker::findByName($post_data['name']);
		if (is_object($name_exists)) {
			Flash::set('error', __('A record with this name already exists.'));
			Flash::set('post_data', $post_data);
			redirect(get_url('plugin/link_tracker/add'));
		}
		
		// Clean data.
		$post_data['name'] = htmlentities($post_data['name']);
		$post_data['url'] = htmlentities($post_data['url']);
		
		// Prepend http:// to URL if not there already.
		$post_data['url'] = add_prefix($post_data['url'], 'http://');
		
		// Create tracker.
		$tracker = new LinkTracker($post_data);
		if (!$tracker->save()) {
			Flash::set('error', __('Unable to create record.'));
			Flash::set('post_data', $post_data);
			redirect(get_url('plugin/link_tracker/add'));
		}
		else {			
			Flash::set('success', __('Record has been created.'));
			
			$submit = $_POST['submit'];
			
			if (isset($submit['save'])) {
				redirect(get_url('plugin/link_tracker'));
			} else {
				redirect(get_url('plugin/link_tracker/edit/'.$tracker->id));
			}
		}
	} 
	
	/**
	 * Display the edit tracker form.
	 *
	 * @param int $id
	 * @param int $current_page Used for paginating the click results.
	 */
	public function edit($id, $current_page=1) 
	{ 
		// Process form?
		if (get_request_method() == 'POST') {
			exit($this->_edit($id));
		}
		
		$post_data = Flash::get('post_data');
		if ($post_data) {
			$tracker = $post_data;
		} else {
			$tracker_obj = LinkTracker::findById((int)$id);
			if (!is_object($tracker_obj)) {
				Flash::set('error', __('Record not found.'));
				redirect(get_url('plugin/link_tracker'));
			} else {
				$tracker = (array) $tracker_obj;
			}
		}
		
		$view_data = array(
			'action' => 'edit',
			'form_action' => get_url('plugin/link_tracker/edit/'.$id),
			'pagination_enabled' => false,
			'tracker' => $tracker,
		);
		
		// Get clicks.
		$tracker_clicks = LinkTrackerClick::find(array(
			'where' => "link_tracker_id = $id",
			'order' => 'datetime DESC'
		));
		
		$view_data['tracker_clicks'] = $tracker_clicks;
		
		if (is_array($tracker_clicks) && !empty($tracker_clicks)) 
		{
			$view_data['pagination_enabled'] = true;
			$rows_per_page = 5;
			$offset = ($rows_per_page * ($current_page - 1));
			$total_row_count = LinkTrackerClick::countAll();
			$total_page_count = ceil($total_row_count / $rows_per_page);
			$view_data = array_merge($view_data, array(
				'pg_rows_per_page' => $rows_per_page,
				'pg_current_page' => $current_page,
				'pg_total_row_count' => $total_row_count,
				'pg_total_page_count' => $total_page_count		
			));
			
			$tracker_clicks = LinkTrackerClick::find(array(
				'where' => "link_tracker_id = $id",
				'order' => 'datetime DESC',
				'limit' => $rows_per_page,
				'offset' => $offset
			));
			
			$view_data['tracker_clicks'] = $tracker_clicks;
		}
		
		$this->display('link_tracker/views/edit', $view_data);
		
	}
	
	/**
	 * Process the edit tracker form.
	 *
	 * @param int $id
	 */
	private function _edit($id) 
	{ 
		// Check post data.
		$post_data_found = (isset($_POST['tracker']) && !empty($_POST['tracker']));
		$post_data = ($post_data_found ? $_POST['tracker'] : false);
		if (!$post_data) {
			Flash::set('error', __('Post data not found.'));
			redirect(get_url('plugin/link_tracker/add'));
		}
		
		// Check valid fields.
		if (!isset($post_data['name']) || empty($post_data['name'])) {
			Flash::set('error', __('Please enter a name.'));
			Flash::set('post_data', $post_data);
			redirect(get_url('plugin/link_tracker/add'));
		}
		if (!isset($post_data['url']) || empty($post_data['url'])) {
			Flash::set('error', __('Please enter a URL.'));
			Flash::set('post_data', $post_data);
			redirect(get_url('plugin/link_tracker/add'));
		}
		
		// Name taken by another record?
		$name_exists = LinkTracker::findByName($post_data['name'], "id != $id");
		if (is_object($name_exists)) {
			Flash::set('error', __('A record with this name already exists.'));
			Flash::set('post_data', $post_data);
			redirect(get_url('plugin/link_tracker/add'));
		}
		
		// Clean data.
		$post_data['name'] = htmlentities($post_data['name']);
		$post_data['url'] = htmlentities($post_data['url']);
		
		// Prepend http:// to URL if not there already.
		$post_data['url'] = add_prefix($post_data['url'], 'http://');
		
		// Create tracker.
		$tracker = new LinkTracker($post_data);
		$tracker->id = (int) $id;
		if (!$tracker->save()) {
			Flash::set('error', __('Unable to update record.'));
			Flash::set('post_data', $post_data);
			redirect(get_url('plugin/link_tracker/edit/'.$id));
		}
		else {
			Flash::set('success', __('Record has been updated.'));
			
			$submit = $_POST['submit'];
			if (isset($submit['save'])) {
				redirect(get_url('plugin/link_tracker'));
			} else {
				redirect(get_url('plugin/link_tracker/edit/'.$tracker->id));
			}
		}
	}
	
	/**
	 * Reset's the stat fields for a specific record.
	 * 
	 * @param int $id
	 */
	public function reset_stats($id) 
	{
		$record = LinkTracker::findById($id);
		
		if (!is_object($record)) { return false; }
		
		if (!$record->reset_stats()) {
			Flash::set('error', __('Unable to update record.'));
		} else {
			Flash::set('success', __('Record has been updated.'));
		}
		
		redirect(get_url('plugin/link_tracker/edit/'.$id));
	}
	
	/**
	 * Export the click records of a link tracker to a spreadsheet
	 * 
	 * @param int $id
	 */
	public function export_to_excel($id)
	{
		$record = LinkTracker::findById($id);
		
		if (!is_object($record)) { return false; }
		
		$rows = array();
		$headings = array('id','link_tracker_id','ip_address','user_agent','referer','datetime');
		
		$t_id = (int) $id;
		$records = LinkTrackerClick::find(array(
			'where' => "link_tracker_id = $t_id",
			'order' => 'datetime ASC'
		));
		
		if (!is_array($records) || empty($records)) {
			Flash::set('error', __('No data to export.'));
			redirect(get_url('plugin/link_tracker/edit/'.$record->id));
		}
		
		foreach ($records as $record) {
			$rows[] = array(	
				$record->id,
				$record->link_tracker_id,
				$record->ip_address,
				$record->user_agent,
				$record->referer,
				$record->datetime
			);
		}
		
		$filename = 'clicks-'.$record->id.'-'.time().'.xls';
		
		header('Content-type: application/ms-excel');
		header('Content-Disposition: attachment; filename='.$filename);
		
		$content = implode("\t", $headings)."\n";
		foreach ($rows as $row) {
			$content .= implode("\t", $row)."\n";
		}
		
		echo $content;
	}
	
	/**
	 * Display settings form.
	 */
    public function settings() 
	{
		// Call form processor?
		if (get_request_method() == 'POST') {
			exit($this->_settings());
		}
		
		$settings = Plugin::getAllSettings('link_tracker');
		$sort_fields = $this->valid_sort_fields();
        $this->display('link_tracker/views/settings', array(
			'form_action' => get_url('plugin/link_tracker/settings'),
			'sort_fields' => $sort_fields,
			'settings' => $settings
		));
    }
	
	/**
	 * Process settings form.
	 */
	private function _settings()
	{
		// Process settings form.
		if (!isset($_POST['settings']) || empty($_POST['settings'])) {
			Flash::set('error', 'Post data not found.');
			redirect(get_url('plugin/link_tracker/settings'));
		}
		
		$settings = $_POST['settings'];
		
		if (empty($settings['out_slug'])) { $settings['out_slug'] = 'out'; }
		if (empty($settings['rows_per_page'])) { $settings['rows_per_page'] = 10; }
		
		// Verify settings.
		$error = false;
		
		if (!is_numeric($settings['rows_per_page'])) { $error = 'Rows per page must be a numerical value.'; }
		
		// Save settings.
		if (!Plugin::setAllSettings($settings, 'link_tracker')) {
			Flash::set('error', 'Unable to save settings.');
			redirect(get_url('plugin/link_tracker/settings'));
		} else {
			Flash::set('success', 'Settings have been saved!');
			
			$submit = $_POST['submit'];
			
			if (isset($submit['save'])) {
				redirect(get_url('plugin/link_tracker'));
			} else {
				redirect(get_url('plugin/link_tracker/settings'));
			}
		}
	}
	
	/**
	 * Return a list of valid sort table fields.
	 *
	 * @return array
	 */
	private function valid_sort_fields() 
	{
		return array('id','name','url','click_count');
	}

	/**
	 * Documentation page.
	 */
    public function documentation() 
	{
        $this->display('link_tracker/views/documentation');
    }
	
	/**
	 * Testing
	 */
	public function test()
	{
		
	}

} // End of LinkTrackerController.