<?php
/**
 * Main plugin class file.
 *
 * @package /Includes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Main plugin class.
 */
class NELC_Integration {

	/**
	 * The single instance of NELC_Integration.
	 *
	 * @var     object
	 * @access  private
	 * @since   1.0.2
	 */
	private static $_instance = null; //phpcs:ignore

	/**
	 * Local instance of NELC_Integration_Admin_API
	 *
	 * @var NELC_Integration_Admin_API|null
	 */
	public $admin = null;

	/**
	 * Settings class object
	 *
	 * @var     object
	 * @access  public
	 * @since   1.0.2
	 */
	public $settings = null;

	/**
	 * The version number.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.2
	 */
	public $_version; //phpcs:ignore

	/**
	 * The token.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.2
	 */
	public $_token; //phpcs:ignore

	/**
	 * The main plugin file.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.2
	 */
	public $file;

	/**
	 * The main plugin directory.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.2
	 */
	public $dir;

	/**
	 * The plugin assets directory.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.2
	 */
	public $assets_dir;

	/**
	 * The plugin assets URL.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.2
	 */
	public $assets_url;

	/**
	 * Suffix for JavaScripts.
	 *
	 * @var     string
	 * @access  public
	 * @since   1.0.2
	 */
	public $script_suffix;

	/**
	 * Constructor funtion.
	 *
	 * @param string $file File constructor.
	 * @param string $version Plugin version.
	 */
	public function __construct( $file = '', $version = '1.0.2' ) {
		$this->_version = $version;
		$this->_token   = 'nelc_integration';

		// Load plugin environment variables.
		$this->file       = $file;
		$this->dir        = dirname( $this->file );
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );

		$this->script_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		register_activation_hook( $this->file, array( $this, 'install' ) );

		// Load frontend JS & CSS.
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 10 );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10 );

		// Load admin JS & CSS.
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 10, 1 );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ), 10, 1 );

		// Load API for generic admin functions.
		if ( is_admin() ) {
			$this->admin = new NELC_Integration_Admin_API();
		}

		// Handle localisation.
		$this->load_plugin_textdomain();
		add_action( 'init', array( $this, 'load_localisation' ), 0 );

		add_option('lnx_xapi_notific', 'on');
		add_option('lnx_xapi_courses_integrate', 'on');
		add_option('lnx_xapi_complete_profile', __( 'Please complete your profile to be able to enroll in the courses'));

		function lamoud_nelc_xapi_link_courses_box() {
			add_meta_box(
				'lamoud_nelc_xapi_link_courses',
				__( 'NELC Integration', 'lamoud-nelc-xapi' ),
				'lamoud_nelc_xapi_link_courses_html',
				'courses',
				'normal',
				'high'
			);
		}
		add_action( 'add_meta_boxes', 'lamoud_nelc_xapi_link_courses_box' );
		
		function lamoud_nelc_xapi_link_courses_html( $post ) {

			$course_itegrate = get_option('lnx_xapi_courses_integrate');

			add_post_meta( $post->ID, 'lamoud_nelc_xapi_link_course', 'on', true );
			$link_status =  get_post_meta( $post->ID, 'lamoud_nelc_xapi_link_course', true ) === 'on' ? 'checked' : '';
			?>
			<div class="tutor-row tutor-mb-32">
				<div class="tutor-col-12 tutor-col-md-5">
					<label class="tutor-course-setting-label"><?php echo __( 'NELC Integration', 'lamoud-nelc-xapi' ); ?></label>
				</div>
				<div class="tutor-col-12 tutor-col-md-7">

					<?php if( ! $course_itegrate ) : ?>
						<label class="tutor-form-toggle">
							<input id="course_setting_toggle_switch__tutor_is_public_course" type="checkbox" class="tutor-form-toggle-input" name="lamoud_nelc_xapi_link_courses" value="<?php echo $link_status; ?>" <?php echo $link_status; ?>>
								<span class="tutor-form-toggle-control"></span>
						</label>
					<?php else: ?>
						<p>تم تفعيل الربط التلقائي لجميع الدورات</p>
					<?php endif; ?>

					<div class="tutor-fs-7 tutor-has-icon tutor-color-muted tutor-d-flex tutor-mt-12">
						<i class="tutor-icon-circle-info-o tutor-mt-4 tutor-mr-8"></i>
						<?php echo __( 'Upon activation, the courses will linked with NELC.', 'lamoud-nelc-xapi' ); ?>
					</div>
				</div>
			</div>
			<?php




		}

		function save_lamoud_nelc_xapi_link_courses(  $post_id, $post, $update ) {   

			if ( 'courses' !== $post->post_type ) {
				return;
			}

			if ( isset( $_POST['lamoud_nelc_xapi_link_courses'] ) ) {
				$status = esc_url_raw( $_POST['lamoud_nelc_xapi_link_courses'] );
				update_post_meta( $post_id, 'lamoud_nelc_xapi_link_course', 'on' );
			}else{
				update_post_meta( $post_id, 'lamoud_nelc_xapi_link_course', 'of' );
			}

		}
		add_action( 'save_post', 'save_lamoud_nelc_xapi_link_courses', 10,3 );

		add_action('tutor_profile_edit_input_before', 'test_add');
		function test_add($user) {
			global $current_user;
			$user_id = $current_user->ID;
			?>

				<div class="tutor-form-row">
					<div class="tutor-form-col-6">
						<div class="tutor-form-group">
							<label><?php esc_html_e( 'National ID', 'lamoud-nelc-xapi' ); ?></label>
							<input type="text"
								id="nelc_national_id"
								name="nelc_national_id"
								value="<?php echo get_the_author_meta( 'nelc_national_id', $user_id ); ?>"
								class="input"
								required
							/>
						</div>
					</div>
				</div>
			
			<?php
			if(! empty( $_POST['nelc_national_id'] )){
				update_user_meta( $user_id, 'nelc_national_id', intval( $_POST['nelc_national_id'] ) );
			}
		}

		add_action( 'tutor_profile_update_after', 'my_profile_update', 10, 2 );
		function my_profile_update( $user_id) {
			if(! empty( $_POST['nelc_national_id'] )){
				update_user_meta( $user_id, 'nelc_national_id', intval( $_POST['nelc_national_id'] ) );
			}
		}


	} // End __construct ()

	/**
	 * Wrapper function to register a new statment.
	 *
	 * @param string $statment Statment.
	 * @param array  $statment_args statment arguments.
	 *
	 * @return bool|string|NELC_Integration_Statements
	 */
	public function register_statment( $statment = '', $statment_args = array() ) {

		if ( ! $statment || ! $statment_args ) {
			return false;
		}

		$statment = new NELC_Integration_Statements( $statment, $statment_args );

		return $statment->statment_args;
	}
	/**
	 * Wrapper function to register a new Interactions.
	 *
	 * @param string $interactions  Interactions.
	 * @param array  $interactions _args interactions arguments.
	 *
	 * @return bool|string|NELC_Integration_Interactions
	 */
	public function register_interactions( $body = array() ) {

		if ( ! $body ) {
			return false; // return false if no body
		}

		// 
		$interactions  = new NELC_Integration_Interactions( $body );

		// Connect to xapi & send the statments
		$response = wp_remote_post( $interactions->endpoint, array (
			'method'  => 'POST',
			'timeout' => 20,
			'headers' => $interactions->headers,
			'body'    =>  json_encode($interactions->body)
		));

		return $response;
	}

	/**
	 * Load frontend CSS.
	 *
	 * @access  public
	 * @return void
	 * @since   1.0.2
	 */
	public function enqueue_styles() {
		wp_register_style( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'css/frontend.css', array(), $this->_version );
		wp_enqueue_style( $this->_token . '-frontend' );
		
		wp_register_style( $this->_token . '-izitoast', esc_url( $this->assets_url ) . 'izitoast/css/iziToast.min.css', array(), $this->_version );
		wp_enqueue_style( $this->_token . '-izitoast' );
	} // End enqueue_styles ()

	/**
	 * Load frontend Javascript.
	 *
	 * @access  public
	 * @return  void
	 * @since   1.0.2
	 */
	public function enqueue_scripts() {
		wp_register_script( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'js/frontend' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version, true );
		wp_enqueue_script( $this->_token . '-frontend' );
		
		wp_register_script( $this->_token . '-izitoast', esc_url( $this->assets_url ) . 'izitoast/js/iziToast' . $this->script_suffix . '.js', array(), $this->_version, true );
		wp_enqueue_script( $this->_token . '-izitoast' );
	} // End enqueue_scripts ()

	/**
	 * Admin enqueue style.
	 *
	 * @param string $hook Hook parameter.
	 *
	 * @return void
	 */
	public function admin_enqueue_styles( $hook = '' ) {
		wp_register_style( $this->_token . '-admin', esc_url( $this->assets_url ) . 'css/admin.css', array(), $this->_version );
		wp_enqueue_style( $this->_token . '-admin' );
	} // End admin_enqueue_styles ()

	/**
	 * Load admin Javascript.
	 *
	 * @access  public
	 *
	 * @param string $hook Hook parameter.
	 *
	 * @return  void
	 * @since   1.0.2
	 */
	public function admin_enqueue_scripts( $hook = '' ) {
		wp_register_script( $this->_token . '-admin', esc_url( $this->assets_url ) . 'js/admin' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version, true );
		wp_enqueue_script( $this->_token . '-admin' );
	} // End admin_enqueue_scripts ()

	/**
	 * Load plugin localisation
	 *
	 * @access  public
	 * @return  void
	 * @since   1.0.2
	 */
	public function load_localisation() {
		load_plugin_textdomain( 'lamoud-nelc-xapi', false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_localisation ()

	/**
	 * Load plugin textdomain
	 *
	 * @access  public
	 * @return  void
	 * @since   1.0.2
	 */
	public function load_plugin_textdomain() {
		$domain = 'lamoud-nelc-xapi';

		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );

		load_textdomain( $domain, WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
		load_plugin_textdomain( $domain, false, dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_plugin_textdomain ()

	/**
	 * Main NELC_Integration Instance
	 *
	 * Ensures only one instance of NELC_Integration is loaded or can be loaded.
	 *
	 * @param string $file File instance.
	 * @param string $version Version parameter.
	 *
	 * @return Object NELC_Integration instance
	 * @see NELC_Integration()
	 * @since 1.0.2
	 * @static
	 */
	public static function instance( $file = '', $version = '1.0.2' ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $file, $version );
		}

		return self::$_instance;
	} // End instance ()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.2
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html( __( 'Cloning of NELC_Integration is forbidden' ) ), esc_attr( $this->_version ) );

	} // End __clone ()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.2
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html( __( 'Unserializing instances of NELC_Integration is forbidden' ) ), esc_attr( $this->_version ) );
	} // End __wakeup ()

	/**
	 * Installation. Runs on activation.
	 *
	 * @access  public
	 * @return  void
	 * @since   1.0.2
	 */
	public function install() {
		$this->_log_version_number();
	} // End install ()

	/**
	 * Log the plugin version number.
	 *
	 * @access  public
	 * @return  void
	 * @since   1.0.2
	 */
	private function _log_version_number() { //phpcs:ignore
		update_option( $this->_token . '_version', $this->_version );
	} // End _log_version_number ()

}
