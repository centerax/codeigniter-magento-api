<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * CodeIgniter Magento API connector
 *
 * Make Magento SOAP API calls easily with this API class
 *
 * @package        	CodeIgniter
 * @subpackage    	Libraries
 * @category    	Libraries
 * @author        	Pablo S. Benitez
 * @created			06/06/2011
 * @license         http://www.opensource.org/licenses/mit-license.html
 * @link			http://getsparks.org/packages/mage-api/show
 */

class Mage_api
{

	private $_ci; //CodeIgniter instance

	private $_api_instance; //Magento API instance
	private $_api_session_key; //Magento login session key
	private $_api_username; //API Username
	private $_api_password; //API Password
	private $_api_wsdl_uri; //Magento API WSDL document uri
	private $_api_options = array();

	function __construct($options = array())
	{
		$this->_ci =& get_instance();
		log_message('debug', 'Mage API Class Initialized');


		//Load config
		$this->_ci->load->config('mage_api');

		$this->_api_wsdl_uri = $this->_ci->config->item('magento_wsdl_uri');
		$this->_api_username = $this->_ci->config->item('magento_api_username');
		$this->_api_password = $this->_ci->config->item('magento_api_password');

		if ( FALSE === $this->can_run() )
		{
			$error_message = 'Mage API Class - PHP was not built with SOAP enabled.';
			show_error($error_message);
			log_message('error', $error_message);
		}

		if( count($options) ){
			$this->_api_options = array_merge($this->_api_options, $options);
		}

		$this->initialize();
	}

	/**
	 * Catches all undefined methods
	 * @param	string	command to be run
	 * @param	array	arguments to be passed
	 * @return 	mixed
	 */
	public function __call($method, $args = array())
	{
		$command = substr_replace($method, '.', strrpos($method, '_'), 1);

		log_message('debug', $command);
		log_message('debug', print_r($args, TRUE));

		return $this->call( $command, $args );
	}

	public function can_run()
	{
		return extension_loaded('soap');
	}

	public function initialize()
	{
		$this->_api_instance = new SoapClient($this->_api_wsdl_uri, $this->_api_options);

		try{

			$this->_api_session_key = $this->_api_instance->login($this->_api_username, $this->_api_password);

		}catch(SoapFault $soap_ex){

			log_message('error', $soap_ex->getMessage());
			show_error($this->_error_msg($soap_ex->getMessage()));

		}catch(Exception $ex){

			log_message('error', $ex->getMessage());
			show_error($this->_error_msg($soap_ex->getMessage()));

		}
	}

	/**
	 * Perform API call, also can be used "manually"
	 *
	 *@param string $command Command to be performed
	 *@param optional array $args Call parameters
	 *@return array Api call result
	 */
	public function call($command, $args = array())
	{
		try{
			return $this->_api_instance->call($this->_api_session_key, $command, $args);
		}catch(SoapFault $soap_ex){

			log_message('error', $soap_ex->getMessage());
			log_message('debug', $this->get_last_request());

			show_error($this->_error_msg($soap_ex->getMessage()));

		}catch(Exception $ex){

			log_message('error', $ex->getMessage());
			log_message('debug', $this->get_last_request());

			show_error($this->_error_msg($soap_ex->getMessage()));

		}
		return FALSE;
	}

	public function get_last_request()
	{
		return $this->_api_instance->__getLastRequest();
	}

	protected function _error_msg($text)
	{
		return sprintf('Magento API ERROR: %s', $text);
	}

}