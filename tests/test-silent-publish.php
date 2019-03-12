<?php

defined( 'ABSPATH' ) or die();

class Silent_Publish_Test extends WP_UnitTestCase {

	protected $field    = 'silent_publish';
	protected $meta_key = '_silent-publish';
	protected $nonce    = '_silent_publish_nonce';

	private   $hooked   = -1;

	public function tearDown() {
		parent::tearDown();

		$this->hooked = -1;

		add_action( 'publish_post', '_publish_post_hook', 5, 1 );

		remove_filter( 'c2c_silent_publish_meta_key', array( $this, 'c2c_silent_publish_meta_key' ) );
		remove_filter( 'c2c_silent_publish_meta_key', '__return_empty_string' );
		remove_filter( 'c2c_silent_publish_default',  '__return_true' );
		remove_action( 'publish_post',                array( $this, 'check_publish_post_hook' ), 4, 1 );
		remove_action( 'publish_post',                array( $this, 'check_publish_post_hook' ), 6, 1 );
	}


	//
	//
	// HELPER FUNCTIONS
	//
	//


	public function c2c_silent_publish_meta_key( $key ) {
		return '_new-key';
	}

	public function check_publish_post_hook( $post_id ) {
		$this->hooked = has_action( 'publish_post', '_publish_post_hook', 5, 1 ) ? 1 : 2;
	}

	/**
	 * Asserts the output matches the expected output.
	 *
	 * @param string $output            The output of the form markup for Silent Publish.
	 * @param bool   $is_silent_publish Is the value of the silent publish checkbox checked?
	 * @param bool   $is_published      Is the post published?
	 */
	public function assert_form_output( $output, $is_silent_publish, $is_published ) {
		$expected = '';

		if ( ! $is_silent_publish && $is_published ) {
			return $expected;
		}

		$expected .= sprintf(
			'<div class="misc-pub-section"><label class="selectit c2c-silent-publish" for="%1$s" title="%2$s"%3$s>' . "\n",
			esc_attr( $this->field ),
			esc_attr__( 'If checked, upon publication of this post do not perform any pingbacks, trackbacks, or update service notifications.', 'silent-publish' ),
			$is_published ? ' style="opacity:.7"' : ''
		);

		$expected .= sprintf( '<input type="hidden" name="_%1$s_nonce" value="%2$s" />', $this->field, wp_create_nonce( $this->field ) );

		// Output input field.
		$expected .= sprintf(
			'<input id="%1$s" type="checkbox" %2$s %3$s value="1" name="%4$s" />' . "\n",
			esc_attr( $this->field ),
			disabled( $is_published, true, false ),
			checked( true, (bool) $is_silent_publish, false ),
			esc_attr( $this->field )
		);

		$expected .= __( 'Silent publish?', 'silent-publish' );
		$expected .= '</label></div>' . "\n";

		$this->assertEquals( $expected, $output );
	}

	//
	//
	// TESTS
	//
	//


	public function test_class_exists() {
		$this->assertTrue( class_exists( 'c2c_SilentPublish' ) );
	}

	public function test_version() {
		$this->assertEquals( '2.6.1', c2c_SilentPublish::version() );
	}

	public function test_plugins_loaded_action_triggers_do_init() {
		$this->assertNotFalse( has_filter( 'plugins_loaded', array( 'c2c_SilentPublish', 'init' ) ) );
	}

	public function test_post_submitbox_misc_action_triggers_add_ui() {
		$this->assertNotFalse( has_action( 'post_submitbox_misc_actions', array( 'c2c_SilentPublish', 'add_ui' ) ) );
	}

	public function test_save_post_filter_triggers_save_silent_publish_status() {
		$this->assertNotFalse( has_filter( 'save_post', array( 'c2c_SilentPublish', 'save_silent_publish_status' ), 2, 3 ) );
	}

	public function test_publish_post_action_triggers_publish_post() {
		$this->assertNotFalse( has_action( 'publish_post', array( 'c2c_SilentPublish', 'publish_post' ), 1, 1 ) );
	}

	public function test_non_silently_published_post_publishes_without_silencing() {
		$post_id = $this->factory->post->create( array( 'post_status' => 'draft' ) );

		wp_publish_post( $post_id );

		$this->assertFalse( metadata_exists( 'post', $post_id, $this->meta_key ) );
		$this->assertEquals( 5, has_action( 'publish_post', '_publish_post_hook', 5, 1 ) );

		return $post_id;
	}

	public function test_saving_post_set_as_silently_published_retains_meta() {
		$post_id = $this->factory->post->create();
		update_post_meta( $post_id, $this->meta_key, '1' );

		$this->assertTrue( metadata_exists( 'post', $post_id, $this->meta_key ) );

		$post = get_post( $post_id, ARRAY_A );
		$_POST[ $this->field ] = '1';
		wp_update_post( $post );

		$this->assertTrue( metadata_exists( 'post', $post_id, $this->meta_key ) );
		$this->assertEquals( '1', get_post_meta( $post_id, $this->meta_key, true ) );
	}

	public function test_saving_post_without_being_silently_published_deletes_meta() {
		$post_id = $this->factory->post->create();
		update_post_meta( $post_id, $this->meta_key, '1' );

		$this->assertTrue( metadata_exists( 'post', $post_id, $this->meta_key ) );
		$this->assertEquals( '1', get_post_meta( $post_id, $this->meta_key, true ) );

		$post = get_post( $post_id, ARRAY_A );
		// Simulate a POST.
		$_POST[ $this->nonce ] = wp_create_nonce( $this->field );
		wp_update_post( $post );

		$this->assertFalse( metadata_exists( 'post', $post_id, $this->meta_key ) );
	}

	public function test_saving_post_explicitly_not_being_silently_published_deletes_meta() {
		$post_id = $this->factory->post->create();
		update_post_meta( $post_id, $this->meta_key, '1' );

		$this->assertTrue( metadata_exists( 'post', $post_id, $this->meta_key ) );
		$this->assertEquals( '1', get_post_meta( $post_id, $this->meta_key, true ) );

		$post = get_post( $post_id, ARRAY_A );
		// Simulate a POST.
		$_POST[ $this->nonce ] = wp_create_nonce( $this->field );
		wp_update_post( $post );

		$this->assertFalse( metadata_exists( 'post', $post_id, $this->meta_key ) );
		$this->assertEquals( 5, has_action( 'publish_post', '_publish_post_hook', 5, 1 ) );
	}

	/*
	 * get_meta_key_name()
	 */

	public function test_get_meta_key_name() {
		$this->assertEquals( '_silent-publish', c2c_SilentPublish::get_meta_key_name() );
	}

	public function test_filtered_get_meta_key_name() {
		add_filter( 'c2c_silent_publish_meta_key', array( $this, 'c2c_silent_publish_meta_key' ) );

		$this->assertEquals( '_new-key', c2c_SilentPublish::get_meta_key_name() );
	}

	public function test_empty_get_meta_key_name() {
		add_filter( 'c2c_silent_publish_meta_key', '__return_empty_string' );

		$this->assertEmpty( c2c_SilentPublish::get_meta_key_name() );
	}

	public function test_silently_published_post_publishes_silently() {
		$post_id = $this->factory->post->create( array( 'post_status' => 'draft' ) );

		// Publishing assumes it's coming from the edit page UI where the
		// checkbox is present to set the $_POST array element to trigger
		// stealth update
		$_POST[ $this->field ] = '1';
		$_POST[ $this->nonce ] = wp_create_nonce( $this->field );

		wp_publish_post( $post_id );

		$this->assertTrue( metadata_exists( 'post', $post_id, $this->meta_key ) );
		$this->assertEquals( '1', get_post_meta( $post_id, $this->meta_key, true ) );
		$this->assertFalse( has_action( 'publish_post', '_publish_post_hook', 5, 1 ) );

		return $post_id;
	}

	public function test_silently_published_post_via_meta_on_draft_publishes_silently() {
		$post_id = $this->factory->post->create( array( 'post_status' => 'draft' ) );
		update_post_meta( $post_id, $this->meta_key, '1' );

		wp_publish_post( $post_id );

		$this->assertTrue( metadata_exists( 'post', $post_id, $this->meta_key ) );
		$this->assertEquals( '1', get_post_meta( $post_id, $this->meta_key, true ) );
		$this->assertFalse( has_action( 'publish_post', '_publish_post_hook', 5, 1 ) );
	}

	public function test_previously_silently_published_post_can_be_republished_without_silence() {
		$post_id = $this->test_silently_published_post_publishes_silently();

		// Publishing assumes it's coming from the edit page UI where the
		// checkbox is present to set the $_POST array element to trigger
		// stealth update
		unset( $_POST[ $this->field ] );
		$_POST[ $this->nonce ] = wp_create_nonce( $this->field );

		$post = get_post( $post_id, ARRAY_A );
		$post['post_status'] = 'draft';
		$post_id = wp_update_post( $post, true );

		wp_publish_post( $post_id );

		$this->assertFalse( metadata_exists( 'post', $post_id, $this->meta_key ) );
		$this->assertEquals( 5, has_action( 'publish_post', '_publish_post_hook', 5, 1 ) );
	}

	/*
	 * add_ui()
	 */

	public function test_form_elements_are_output_for_unpublished_post() {
		global $post;

		$post_id = $this->factory->post->create( array( 'post_status' => 'draft' ) );
		$post = get_post( $post_id );

		ob_start();
		c2c_SilentPublish::add_ui();
		$output = ob_get_contents();
		ob_end_clean();

		$this->assertNotEmpty( $output );
		$this->assert_form_output( $output, false, false );
	}

	public function test_form_elements_are_output_for_unpublished_post_with_meta_set() {
		global $post;

		$post_id = $this->factory->post->create( array( 'post_status' => 'draft' ) );
		update_post_meta( $post_id, $this->meta_key, '1' );
		$post = get_post( $post_id );

		ob_start();
		c2c_SilentPublish::add_ui();
		$output = ob_get_contents();
		ob_end_clean();

		$this->assertNotEmpty( $output );
		$this->assert_form_output( $output, true, false );
	}

	public function test_form_elements_are_output_for_published_post_with_meta_set() {
		global $post;

		$post_id = $this->factory->post->create( array( 'post_status' => 'publish' ) );
		add_post_meta( $post_id, $this->meta_key, '1' );
		$post = get_post( $post_id );

		ob_start();
		c2c_SilentPublish::add_ui();
		$output = ob_get_contents();
		ob_end_clean();

		$this->assertNotEmpty( $output );
		$this->assert_form_output( $output, true, true );
	}

	public function test_form_elements_not_output_hidden_for_published_post_without_meta() {
		global $post;

		$post_id = $this->factory->post->create( array( 'post_status' => 'publish' ) );
		$post = get_post( $post_id );

		ob_start();
		c2c_SilentPublish::add_ui();
		$output = ob_get_contents();
		ob_end_clean();

		$this->assertEmpty( $output );
		$this->assert_form_output( $output, false, true );
	}

	public function test_is_silent_publish_on_by_default() {
		$this->assertFalse( c2c_SilentPublish::is_silent_publish_on_by_default() );
	}

	public function test_filter_c2c_silent_publish_default() {
		add_filter( 'c2c_silent_publish_default', '__return_true' );

		$this->assertTrue( c2c_SilentPublish::is_silent_publish_on_by_default() );
	}

	/*
	 * Check filter gets unhooked.
	 */

	public function test_default_behavior() {
		add_action( 'publish_post', array( $this, 'check_publish_post_hook' ), 4, 1 );

		$post_id = $this->test_non_silently_published_post_publishes_without_silencing();

		do_action( 'publish_post', $post_id );

		$this->assertEquals( 1, $this->hooked );
	}

	public function test_it_works() {
		add_action( 'publish_post', array( $this, 'check_publish_post_hook' ), 4, 1 );

		$post_id = $this->test_silently_published_post_publishes_silently();

		do_action( 'publish_post', $post_id );

		$this->assertEquals( 2, $this->hooked );
	}

}
