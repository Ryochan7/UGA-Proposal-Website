<?php
/**
 * File defines the class Session which will contain data about
 * the current user
 * @package UGA
 */
/**
 *
 */
if (!defined ("IN_APP")) exit ();

require_once (joinPath ("models", "User.php"));

/**
 * Session management class
 *
 * Class contains reference to the current user of a session
 * and other miscellaneous data related to a session
 * @package UGA
 */
class Session {
    /**
     * Message normal status flag
     * @access public
     * @var int
     */
    const MESSAGE_NORMAL = 1;
    /**
     * Message error status flag
     * @access public
     * @var int
     */
    const MESSAGE_ERROR = 2;

    /**
     * Instance of class for Singleton
     * @access protected
     * @static
     * @var Session
     */
    protected static $instance;

    /**
     * Session user
     * @access protected
     * @var User
     */
    protected $user;
    /**
     * Data array for session use
     * @access protected
     * @var array
     */
    protected $data = array ();
    /**
     * Message tied to a session, mainly used for prompts
     * @access protected
     * @var string
     */
    protected $message =  "";
    /**
     * Boolean indicating if a new message has been added
     * @access protected
     * @var bool
     */
    protected $message_change = false;
    /**
     * Integer indicating the type of a session message
     * @access protected
     * @var integer
     */
    protected $message_type = self::MESSAGE_NORMAL;

    /**
     * Retrieve instance of a Session or create one if it does
     * not exist.
     *
     * @access protected
     * @return Session
     */
    public static function getInstance () {
        if (!isset (self::$instance)) {
            self::$instance = new self ();
        }

        return self::$instance;
    }

    /**
     * Private constructor that will call the createSession method
     *
     * @access protected
     */
    private function __construct () {
        $this->createSession ();
    }

    /**
     * Create a Session by reading variables from the $_SESSION superglobal
     * and populate data such as a User object
     *
     * @access protected
     */
    private function createSession () {
        @session_start ();
        if (isset ($_SESSION["userId"]) && ($_SESSION["userId"] != User::NULL_TYPE)) {
            $userDAO = UserDAO::getInstance ();
            $user = $userDAO->load ($_SESSION["userId"]);
            if ($user != null) {
                $this->user = $user;
            }
        }
        if (isset ($_SESSION["message"])) {
            $this->message = $_SESSION["message"];
        }

        if (isset ($_SESSION["data"])) {
            $this->data = unserialize ($_SESSION["data"]);
        }

        if (isset ($_SESSION["message_type"])) {
            $this->setMessageType ($_SESSION["message_type"]);
        }
    }

    /**
     * Destructor method. Method will write current information
     * to session file and then save the changes
     *
     * @access public
     */
    public function __destruct () {
        if ($this->message_change) {
            $_SESSION["message"] = $this->message;
        }
        else {
            $_SESSION["message"] = ""; // Reset Message
        }
        $_SESSION["message_type"] = $this->message_type;
        $_SESSION["data"] = serialize ($this->data);

        if ($this->user != null) {
            $_SESSION["userId"] = $this->user->id;
        }
        else {
            $_SESSION["userId"] = User::NULL_TYPE;
        }

        session_write_close ();
    }

    /**
     * Get User object associated with a Session
     *
     * @access public
     * @return User
     */
    public function getUser () {
        return $this->user;
    }

    /**
     * Get data array for a session
     *
     * @access public
     * @return array
     */
    public function getData () {
        return $this->data;
    }

    /**
     * Get message string for a session
     *
     * @access public
     * @return string
     */
    public function getMessage () {
        return $this->message;
    }

    /**
     * Set data array for the Session
     *
     * @access public
     * @param array $array
     */
    public function setData ($array) {
        if (!is_array ($array)) {
            throw new InvalidArgumentException ("setData expects an array");
        }
        $this->data = $array;
    }

    /**
     * Update an item in the data array
     *
     * @access public
     * @param mixed $key
     * @param mixed $value
     */
    public function updateData ($key, $value) {
        $this->data[$key] = $value;
    }

    /**
     * Get the value for an entry in the data array
     *
     * @access public
     * @param mixed $key
     * @return mixed
     */
    public function getDataValue ($key) {
        return $this->data[$key];
    }

    /**
     * Set message string for a Session. Useful for messages in
     * sequential page requests
     *
     * @access public
     * @param string $msg
     */
    public function setMessage ($msg, $type=self::MESSAGE_NORMAL) {
        if (!is_string ($msg)) {
            throw new InvalidArgumentException ("setMessage expects a string");
        }
        $this->setMessageType ($type);
        $this->message = $msg;
        $this->message_change = true;
    }

    /**
     * Set instance of a User for the Session
     *
     * @access public
     * @param User $user
     * @return array
     */
    public function setUser (User $user) {
        $this->user = $user;
    }

    /**
     * Reset properties of the class (User, message, data array).
     * Also calls session_destroy.
     *
     * @access private
     */
    private function reset () {
        $this->user = null;
        $this->message = "";
        $this->data = array ();
        session_destroy ();
    }

    /**
     * Kills a current PHP session, write out any data,
     * and start a new session for a user
     *
     * @access public
     */
    public function kill () {
        // Generate new session id
        session_regenerate_id ();
        // Retrieve new session id
        $new_session_id = session_id ();
        // Destroy current session data
        $this->reset ();
        // Fully close current session
        session_write_close ();
        // Use new session id for new session (will use old session id otherwise)
        session_id ($new_session_id);
        // Start new session
        $this->createSession ();
    }

    /**
     * Sets the status flag for a set message.
     *
     * @access public
     * @param int $type Use value specified for a message status type
     */
    public function setMessageType ($type) {
        if (!is_int ($type)) {
            throw new InvalidArgumentException ("setMessageType expects an integer");
        }

        switch ($type) {
            case self::MESSAGE_NORMAL:
            case self::MESSAGE_ERROR:
                $this->message_type = $type;
                break;
            default:
                throw new InvalidArgumentException ("An invalid integer was passed to setMessageType");
        }
    }

    /**
     * Get the status flag for a set message
     *
     * @access public
     * @return int
     */
    public function getMessageType () {
        return $this->message_type;
    }

}

?>
