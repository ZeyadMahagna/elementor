<?php
namespace Elementor\Modules\Styleguide;

use Elementor\Core\Base\Module as Base_Module;
use Elementor\Core\Experiments\Manager as Experiments_Manager;
use Elementor\Core\Kits\Controls\Switcher as Global_Style_Switcher;
use Elementor\Core\Settings\Manager as SettingsManager;
use Elementor\Plugin;
use Elementor\Modules\Styleguide\Data\Controller;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class Module extends Base_Module {

	const ASSETS_HANDLE = 'elementor-styleguide';
	const ASSETS_SRC = 'styleguide';

	const EXPERIMENT_NAME = 'e_global_styleguide';

	/**
	 * Initialize the Container-Converter module.
	 *
	 * @return void
	 */
	public function __construct() {
		parent::__construct();
		add_action( 'elementor/editor/after_enqueue_scripts', [ $this, 'enqueue_main_scripts' ] );
		add_action( 'elementor/preview/enqueue_styles', [ $this, 'enqueue_styles' ] );

		add_action( 'elementor/frontend/after_register_scripts', function () {
			$is_preview = Plugin::$instance->preview->is_preview();

			if ( ! $is_preview ) {
				return;
			}

			$this->enqueue_app_initiator( $is_preview );
		} );

		add_action( 'elementor/element/after_section_start', [ $this, 'add_styleguide_enable_controls' ], 10, 3 );

		Plugin::$instance->data_manager_v2->register_controller( new Controller() );
	}

	/**
	 * Retrieve the module name.
	 *
	 * @return string
	 */
	public function get_name() {
		return 'styleguide';
	}

	public static function is_active() {
		return Plugin::$instance->experiments->is_feature_active( self::EXPERIMENT_NAME );
	}

	public static function get_experimental_data() {
		return [
			'name' => static::EXPERIMENT_NAME,
			'title' => esc_html__( 'Global Style Guide', 'elementor' ),
			'tag' => esc_html__( 'Feature', 'elementor' ), //todo : add tag
			'description' => esc_html__( 'Display a live preview of changes to global colors and fonts in a sleek style guide from the site’s settings. You will be able to toggle between the style guide and the page to see your changes in action.', 'elementor' ),
			'release_status' => Experiments_Manager::RELEASE_STATUS_ALPHA,
			'default' => Experiments_Manager::STATE_INACTIVE,
		];
	}

	/**
	 * Enqueue scripts.
	 *
	 * @return void
	 */
	public function enqueue_main_scripts() {
		wp_enqueue_script(
			$this::ASSETS_HANDLE,
			$this->get_js_assets_url( $this::ASSETS_SRC ),
			[ 'elementor-editor' ],
			ELEMENTOR_VERSION,
			true
		);

		$kit_id = Plugin::$instance->kits_manager->get_active_id();

		wp_localize_script( $this::ASSETS_HANDLE, 'elementorStyleguideConfig', [
			'activeKitId' => $kit_id,
		] );
	}

	public function enqueue_app_initiator( $is_preview = false ) {
		$dependencies = [
			'react',
			'react-dom',
		];

		if ( ! $is_preview ) {
			$dependencies[] = $this::ASSETS_HANDLE;
		}

		wp_enqueue_script(
			$this::ASSETS_HANDLE . '-app-initiator',
			$this->get_js_assets_url( $this::ASSETS_SRC . '-app-initiator' ),
			$dependencies,
			ELEMENTOR_VERSION,
			true
		);
	}

	public function enqueue_styles() {
		wp_enqueue_style(
			$this::ASSETS_HANDLE,
			$this->get_css_assets_url( 'modules/' . $this::ASSETS_SRC . '/' . $this::ASSETS_SRC ),
			[],
			ELEMENTOR_VERSION
		);
	}

	/**
	 * Check whether the user has Styleguide Preview enabled.
	 *
	 * @return bool
	 */
	private function is_styleguide_preview_enabled(): bool {
		$editor_preferences = SettingsManager::get_settings_managers( 'editorPreferences' )->get_model();

		return $editor_preferences->get_settings( 'enable_styleguide_preview' );
	}

	public function add_styleguide_enable_controls( $element, $section_id, $args ) {
		if ( 'kit' !== $element->get_name() || ! in_array( $section_id, [ 'section_global_colors', 'section_text_style' ] ) ) {
			return;
		}

		$element->add_control(
			$section_id . '_enable_styleguide_preview',
			[
				'label' => esc_html__( 'Style Guide Preview', 'elementor' ),
				'type' => Global_Style_Switcher::CONTROL_TYPE,
				'description' => esc_html__( 'Switch between the content area and style guide to preview your changes to global colors.', 'elementor' ),
				'separator' => 'after',
				'label_off' => esc_html__( 'Off', 'elementor' ),
				'label_on' => esc_html__( 'On', 'elementor' ),
				'default' => $this->is_styleguide_preview_enabled() ? 'yes' : 'no',
				'value' => $this->is_styleguide_preview_enabled() ? 'yes' : 'no',
				'on_change_command' => 'preview/styleguide/enable',
			]
		);

	}
}
