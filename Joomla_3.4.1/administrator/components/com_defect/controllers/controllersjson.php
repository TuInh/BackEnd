<?php 

class itrControllerJson extends JControllerLegacy {

	/** @var array the response to the client */
	protected $response = array();

	public function addResponse($type, $message, $status=200) {

		array_push($this->response, array(
		'status' => $status,
		'type' => $type,
		'data' => $message
		));

	}

	/**
	 * Outputs the response
	 * @return JControllerLegacy|void
	 */
	public function display($cachable = false, $urlparams = array()) {
		
		$response = array(
				'status' => 200,
				'type' => 'multiple',
				'count' => count($this->response),
				'messages' => $this->response
		);
		
		echo json_encode($response);
		jexit();
	}

}
